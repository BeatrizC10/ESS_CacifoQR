<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LockerOpened implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $lockerId;
    public string $action;

    /**
     * Create a new event instance.
     */
    public function __construct(int $lockerId)
    {
        $this->lockerId = $lockerId;
        $this->action = 'OPEN';
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Define um canal público para o cacifo específico
        return [
            new Channel('locker.' . $this->lockerId),
        ];
    }

    /**
     * O nome do evento que o JavaScript vai escutar.
     */
    public function broadcastAs(): string
    {
        return 'LockerOpenedEvent';
    }
}
