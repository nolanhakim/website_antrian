<?php

namespace App\Http\Controllers;

use App\Events\QueueCreated;
use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function kiosk(): View
    {
        return view('queue.kiosk', [
            'categories' => config('antrianq.categories'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prefix' => ['required', 'string', 'in:'.implode(',', array_keys(config('antrianq.categories')))],
            'customer_name' => ['nullable', 'string', 'max:50'],
        ]);

        $queue = Queue::issue($validated['prefix'], $validated['customer_name'] ?? null);

        QueueCreated::dispatch($queue);

        return redirect()->route('ticket', $queue);
    }

    public function ticket(Queue $queue): View
    {
        return view('queue.ticket', [
            'queue' => $queue,
            'service' => config('antrianq.categories.'.$queue->prefix),
            'ahead' => $queue->waitingAhead(),
            'minutes' => $queue->estimatedMinutes(),
        ]);
    }

    public function ticketStatus(Queue $queue): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $queue->status,
            'counter' => $queue->counter?->name,
            'ahead' => $queue->waitingAhead(),
            'minutes' => $queue->estimatedMinutes(),
        ]);
    }

    public function display(): View
    {
        return view('queue.display', Queue::displayState());
    }

    /**
     * JSON fallback kalau WebSocket putus.
     */
    public function displayState(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Queue::displayState());
    }
}
