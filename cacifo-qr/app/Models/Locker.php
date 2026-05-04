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
        'open_command',
    ];

    protected $casts = [
        'door_open' => 'boolean',
        'open_command' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function logs()
    {
        return $this->hasMany(LockerLog::class);
    }
}
