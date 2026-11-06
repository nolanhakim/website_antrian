<?php

namespace Tests\Feature;

use App\Events\QueueCalled;
use App\Models\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QueueNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_numbers_are_sequential_per_prefix_and_reset_daily(): void
    {
        $this->travelTo(Carbon::parse('2026-11-05 09:00:00'));

        $this->assertSame('A-001', Queue::issue('A')->queue_number);
        $this->assertSame('A-002', Queue::issue('A')->queue_number);
        $this->assertSame('B-001', Queue::issue('B')->queue_number);

        $this->travelTo(Carbon::parse('2026-11-06 09:00:00'));

        $this->assertSame('A-001', Queue::issue('A')->queue_number);
    }

    public function test_daily_reset_follows_local_midnight(): void
    {
        $this->travelTo(Carbon::parse('2026-11-05 23:59:00', 'Asia/Jakarta'));
        Queue::issue('A');

        $this->travelTo(Carbon::parse('2026-11-06 00:01:00', 'Asia/Jakarta'));

        $this->assertSame('A-001', Queue::issue('A')->queue_number);
    }

    public function test_kiosk_creates_a_waiting_ticket(): void
    {
        $response = $this->post('/queue', ['prefix' => 'A', 'customer_name' => 'Budi']);

        $queue = Queue::firstOrFail();

        $response->assertRedirect(route('ticket', $queue));
        $this->assertSame(Queue::STATUS_WAITING, $queue->status);
        $this->assertSame('Budi', $queue->customer_name);
    }

    public function test_kiosk_rejects_unknown_prefix(): void
    {
        $this->post('/queue', ['prefix' => 'Z'])->assertSessionHasErrors('prefix');

        $this->assertDatabaseCount('queues', 0);
    }

    /**
     * Regresi: properti publik bernama "queue" pada event dibaca Laravel
     * sebagai nama queue tujuan broadcast.
     */
    public function test_called_event_can_be_queued_for_broadcast(): void
    {
        config(['broadcasting.default' => 'reverb', 'queue.default' => 'database']);

        QueueCalled::dispatch(Queue::issue('A'));

        $this->assertDatabaseCount('jobs', 1);
    }
}
