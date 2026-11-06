<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        Queue::issue('A');

        $this->get('/')->assertOk()->assertSee('Ambil nomor antrean');
        $this->get('/queue/display')->assertOk()->assertSee('Nomor dipanggil');
        $this->get('/login')->assertOk()->assertSee('Masuk untuk staf');
    }

    public function test_operator_page_requires_login(): void
    {
        $this->get('/operator/counter')->assertRedirect('/login');
    }

    public function test_admin_pages_render_and_are_admin_only(): void
    {
        $counter = Counter::create(['name' => 'Loket 1']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@antrianq.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $operator = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@antrianq.test',
            'password' => 'password',
            'role' => 'operator',
            'counter_id' => $counter->id,
        ]);

        $this->actingAs($operator)->get('/admin/dashboard')->assertForbidden();

        $this->actingAs($admin);
        $this->get('/admin/dashboard')->assertOk()->assertSee('Dashboard pemilik');
        $this->get('/admin/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->get('/admin/counters')->assertOk()->assertSee('Loket 1');
        $this->get('/admin/staff')->assertOk()->assertSee('kasir@antrianq.test');
    }

    public function test_login_throttles_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'wrong@antrianq.test', 'password' => 'wrong']);
        }

        $this->post('/login', ['email' => 'wrong@antrianq.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Terlalu banyak',
            session('errors')->first('email')
        );
    }

    public function test_operator_without_counter_is_forbidden(): void
    {
        $operator = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@antrianq.test',
            'password' => 'password',
            'role' => 'operator',
        ]);

        $this->actingAs($operator)->get('/operator/counter')->assertForbidden();
    }

    public function test_admin_can_delete_staff(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin.test@antrianq.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $operator = User::create([
            'name' => 'Operator Delete',
            'email' => 'op.del@antrianq.test',
            'password' => 'password',
            'role' => 'operator',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.staff.destroy', $operator))
            ->assertRedirect()
            ->assertSessionHas('status', 'Staf dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $operator->id]);
    }

    public function test_admin_can_reset_today_queues(): void
    {
        $admin = User::create([
            'name' => 'Admin Reset',
            'email' => 'admin.reset@antrianq.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        Queue::issue('A');
        Queue::issue('A');
        $this->assertSame(2, Queue::today()->count());

        $this->actingAs($admin)
            ->post(route('admin.queues.reset'))
            ->assertRedirect()
            ->assertSessionHas('status', 'Antrean hari ini berhasil direset.');

        $this->assertSame(0, Queue::today()->count());
    }
}
