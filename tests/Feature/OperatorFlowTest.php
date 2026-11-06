<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorFlowTest extends TestCase
{
    use RefreshDatabase;

    private Counter $counter;

    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->counter = Counter::create(['name' => 'Loket 1']);
        $this->operator = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@antrianq.test',
            'password' => 'password',
            'role' => 'operator',
            'counter_id' => $this->counter->id,
        ]);
    }

    public function test_next_finish_and_skip_change_queue_status(): void
    {
        $first = Queue::issue('A');
        $second = Queue::issue('A');

        $this->actingAs($this->operator)->post('/operator/next')->assertRedirect();

        $this->assertSame(Queue::STATUS_CALLED, $first->fresh()->status);
        $this->assertSame($this->counter->id, $first->fresh()->counter_id);

        $this->post('/operator/finish')->assertRedirect();
        $this->assertSame(Queue::STATUS_COMPLETED, $first->fresh()->status);
        $this->assertNotNull($first->fresh()->completed_at);

        $this->post('/operator/next')->assertRedirect();
        $this->assertSame(Queue::STATUS_CALLED, $second->fresh()->status);

        $calledAtBefore = $second->fresh()->called_at;
        $this->travel(2)->seconds();
        $this->post('/operator/recall')->assertRedirect();
        $this->assertTrue($second->fresh()->called_at->gt($calledAtBefore));

        $this->post('/operator/skip')->assertRedirect();
        $this->assertSame(Queue::STATUS_SKIPPED, $second->fresh()->status);
    }

    public function test_operator_skips_other_service_queues(): void
    {
        Queue::issue('B');
        $mine = Queue::issue('A');

        $this->actingAs($this->operator)->post('/operator/next');

        $this->assertSame(Queue::STATUS_WAITING, Queue::where('prefix', 'B')->first()->status);
        $this->assertSame(Queue::STATUS_CALLED, $mine->fresh()->status);
    }

    public function test_skipped_ticket_can_be_recalled(): void
    {
        $queue = Queue::issue('A');

        $this->actingAs($this->operator)->post('/operator/next');
        $this->post('/operator/skip');
        $this->assertSame(Queue::STATUS_SKIPPED, $queue->fresh()->status);

        $this->post(route('operator.recallSkipped', $queue))->assertRedirect();

        $this->assertSame(Queue::STATUS_CALLED, $queue->fresh()->status);
        $this->assertSame($this->counter->id, $queue->fresh()->counter_id);
    }

    public function test_cannot_recall_skipped_ticket_from_other_service(): void
    {
        $queue = Queue::issue('B');
        $queue->update(['status' => Queue::STATUS_SKIPPED]);

        $this->actingAs($this->operator)
            ->post(route('operator.recallSkipped', $queue))
            ->assertForbidden();
    }

    public function test_ticket_shows_queue_position(): void
    {
        $queue = Queue::issue('A');
        Queue::issue('A');

        $this->get(route('ticket', $queue))
            ->assertOk()
            ->assertSee('Tiket A-001')
            ->assertSee('A-001')
            ->assertSee('1');
    }

    public function test_ticket_status_json_endpoint(): void
    {
        $queue = Queue::issue('A');

        $this->get(route('ticket.status', $queue))
            ->assertOk()
            ->assertJson([
                'status' => 'waiting',
                'ahead' => 0,
                'minutes' => 0,
            ]);
    }
}
