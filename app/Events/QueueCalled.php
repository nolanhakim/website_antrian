<?php

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Queue $ticket) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('queue')];
    }

    public function broadcastAs(): string
    {
        return 'queue.called';
    }

    /**
     * Payload ini juga dipakai TV display untuk mengganti seluruh tampilan.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return Queue::displayState();
    }
}
