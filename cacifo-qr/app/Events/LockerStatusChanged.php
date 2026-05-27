<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class LockerStatusChanged implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public int $lockerId, public string $status) {}

    public function broadcastOn(): array
    {
        return [new Channel('locker.' . $this->lockerId)];
    }

    public function broadcastAs(): string
    {
        return 'LockerStatusChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'locker_id' => $this->lockerId,
            'status'    => $this->status,
        ];
    }
}
