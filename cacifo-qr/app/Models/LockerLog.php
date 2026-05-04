<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockerLog extends Model
{
    protected $fillable = [
        'locker_id',
        'user_id',
        'event',
        'description',
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
