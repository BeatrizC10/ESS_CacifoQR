<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
        'door_open',
    ];

    protected $casts = [
        'door_open' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function logs()
    {
        return $this->hasMany(LockerLog::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function simulateOpen(): void
    {
        $this->update([
            'status' => 'open',
            'door_open' => true
        ]);
    }

    public function simulateClose(): void
    {
        $this->update([
            'status' => 'reserved',
            'door_open' => false
        ]);
    }
}
