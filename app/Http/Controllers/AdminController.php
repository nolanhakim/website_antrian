<?php

namespace App\Http\Controllers;

use App\Events\QueueReset;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $queues = Queue::today()->with('counter')->get();

        $completed = $queues->where('status', Queue::STATUS_COMPLETED);
        $called = $queues->whereNotNull('called_at');

        $peakHours = collect(range(8, 21))
            ->mapWithKeys(fn (int $hour) => [
                str_pad((string) $hour, 2, '0', STR_PAD_LEFT).':00' => $queues
                    ->filter(fn (Queue $queue) => (int) $queue->created_at->format('H') === $hour)
                    ->count(),
            ]);

        return view('admin.dashboard', [
            'total' => $queues->count(),
            'completedCount' => $completed->count(),
            'skippedCount' => $queues->where('status', Queue::STATUS_SKIPPED)->count(),
            'waitingCount' => $queues->where('status', Queue::STATUS_WAITING)->count(),
            'avgWait' => (int) round($called->avg(fn (Queue $queue) => $queue->created_at->diffInMinutes($queue->called_at)) ?? 0),
            'avgService' => (int) round($completed->avg(fn (Queue $queue) => $queue->called_at?->diffInMinutes($queue->completed_at) ?? 0) ?? 0),
            'peakHours' => $peakHours,
            'queues' => Queue::today()->with('counter')->latest('id')->take(10)->get(),
        ]);
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $queues = Queue::today()->with('counter')->orderBy('id')->get();
        $filename = 'antrianq-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($queues) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nomor', 'Layanan', 'Nama Pelanggan', 'Loket', 'Status', 'Diambil', 'Dipanggil', 'Selesai']);

            foreach ($queues as $q) {
                fputcsv($handle, [
                    $q->queue_number,
                    config('antrianq.categories.'.$q->prefix, $q->prefix),
                    $q->customer_name ?? '-',
                    $q->counter?->name ?? '-',
                    $q->status,
                    $q->created_at->format('H:i:s'),
                    $q->called_at?->format('H:i:s') ?? '-',
                    $q->completed_at?->format('H:i:s') ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function counters(): View
    {
        return view('admin.counters', [
            'counters' => Counter::withCount('users')->orderBy('id')->get(),
            'categories' => config('antrianq.categories'),
        ]);
    }

    public function storeCounter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'prefix' => ['required', Rule::in(array_keys(config('antrianq.categories')))],
        ]);

        Counter::create($validated);

        return back()->with('status', 'Loket ditambahkan.');
    }

    public function toggleCounter(Counter $counter): RedirectResponse
    {
        $counter->update(['is_active' => ! $counter->is_active]);

        return back()->with('status', $counter->name.' '.($counter->is_active ? 'dibuka.' : 'ditutup.'));
    }

    public function destroyCounter(Counter $counter): RedirectResponse
    {
        $counter->delete();

        return back()->with('status', 'Loket dihapus.');
    }

    public function staff(): View
    {
        return view('admin.staff', [
            'staff' => User::with('counter')->orderBy('name')->get(),
            'counters' => Counter::orderBy('name')->get(),
        ]);
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'operator'])],
            'counter_id' => ['nullable', 'exists:counters,id'],
        ]);

        User::create($validated);

        return back()->with('status', 'Staf ditambahkan.');
    }

    public function updateStaff(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'operator'])],
            'counter_id' => ['nullable', 'exists:counters,id'],
        ]);

        if ($user->is($request->user())) {
            $validated['role'] = $user->role;
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('status', 'Staf diperbarui.');
    }

    public function destroyStaff(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('status', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('status', 'Staf dihapus.');
    }

    public function resetQueues(): RedirectResponse
    {
        Queue::today()->delete();

        QueueReset::dispatch();

        return back()->with('status', 'Antrean hari ini berhasil direset.');
    }
}
