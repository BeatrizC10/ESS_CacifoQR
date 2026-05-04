<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'locker_id',
        'starts_at',
        'ends_at',
        'status',
        'qr_token',
        'qr_expires_at',
        'used',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'qr_expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
