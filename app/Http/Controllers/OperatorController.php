<?php

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Models\Counter;
use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorController extends Controller
{
    public function index(): View
    {
        $counter = $this->counter();

        return view('operator.counter', [
            'counter' => $counter,
            'current' => Queue::today()
                ->where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_CALLED)
                ->latest('called_at')
                ->first(),
            'waiting' => Queue::today()->waiting()->where('prefix', $counter->prefix)->take(8)->pluck('queue_number'),
            'done' => Queue::today()->where('counter_id', $counter->id)->where('status', Queue::STATUS_COMPLETED)->count(),
            'skipped' => Queue::today()->where('counter_id', $counter->id)->where('status', Queue::STATUS_SKIPPED)->count(),
            'skippedToday' => Queue::today()
                ->where('prefix', $counter->prefix)
                ->where('status', Queue::STATUS_SKIPPED)
                ->orderBy('id')
                ->take(8)
                ->get(),
        ]);
    }

    public function next(): RedirectResponse
    {
        $counter = $this->counter();
        $current = $this->current($counter);

        if ($current) {
            $current->update(['status' => Queue::STATUS_COMPLETED, 'completed_at' => now()]);
        }

        $queue = Queue::today()->waiting()->where('prefix', $counter->prefix)->first();

        if (! $queue) {
            return back()->with('status', 'Tidak ada antrean yang menunggu.');
        }

        $queue->update([
            'status' => Queue::STATUS_CALLED,
            'counter_id' => $counter->id,
            'called_at' => now(),
        ]);

        QueueCalled::dispatch($queue);

        return back()->with('status', "{$queue->queue_number} dipanggil ke {$counter->name}.");
    }

    public function recall(): RedirectResponse
    {
        $queue = $this->current($this->counter());

        if (! $queue) {
            return back()->with('status', 'Belum ada nomor yang sedang dipanggil.');
        }

        $queue->update(['called_at' => now()]);

        QueueCalled::dispatch($queue);

        return back()->with('status', "{$queue->queue_number} dipanggil ulang.");
    }

    public function skip(): RedirectResponse
    {
        $queue = $this->current($this->counter());

        if (! $queue) {
            return back()->with('status', 'Belum ada nomor yang sedang dipanggil.');
        }

        $queue->update(['status' => Queue::STATUS_SKIPPED]);

        return back()->with('status', "{$queue->queue_number} dilewati.");
    }

    public function recallSkipped(Queue $queue): RedirectResponse
    {
        $counter = $this->counter();

        abort_unless($queue->created_at->isToday() && $queue->prefix === $counter->prefix, 403);

        if ($queue->status !== Queue::STATUS_SKIPPED) {
            return back()->with('status', "{$queue->queue_number} tidak sedang dilewati.");
        }

        $this->current($counter)?->update(['status' => Queue::STATUS_COMPLETED, 'completed_at' => now()]);

        $queue->update([
            'status' => Queue::STATUS_CALLED,
            'counter_id' => $counter->id,
            'called_at' => now(),
        ]);

        QueueCalled::dispatch($queue);

        return back()->with('status', "{$queue->queue_number} dipanggil ulang ke {$counter->name}.");
    }

    public function finish(): RedirectResponse
    {
        $queue = $this->current($this->counter());

        if (! $queue) {
            return back()->with('status', 'Belum ada nomor yang sedang dipanggil.');
        }

        $queue->update(['status' => Queue::STATUS_COMPLETED, 'completed_at' => now()]);

        return back()->with('status', "{$queue->queue_number} selesai.");
    }

    public function state(): \Illuminate\Http\JsonResponse
    {
        $counter = $this->counter();

        return response()->json([
            'waiting' => Queue::today()
                ->waiting()
                ->where('prefix', $counter->prefix)
                ->take(8)
                ->pluck('queue_number')
                ->values(),
            'done' => Queue::today()
                ->where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_COMPLETED)
                ->count(),
            'skipped' => Queue::today()
                ->where('counter_id', $counter->id)
                ->where('status', Queue::STATUS_SKIPPED)
                ->count(),
        ]);
    }

    private function counter(): Counter
    {
        return auth()->user()->counter ?? abort(403, 'Akun ini belum ditugaskan ke loket.');
    }

    private function current(Counter $counter): ?Queue
    {
        return Queue::today()
            ->where('counter_id', $counter->id)
            ->where('status', Queue::STATUS_CALLED)
            ->latest('called_at')
            ->first();
    }
}
