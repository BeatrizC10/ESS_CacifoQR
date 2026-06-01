<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'alert_sent',
        'amount_paid',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'qr_expires_at' => 'datetime',
        'used' => 'boolean',
        'alert_sent' => 'boolean',
    ];

    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Verifica se o QR Code ainda está dentro da validade e não foi usado
    public function isQrValid(): bool
    {
        return !$this->used &&
            $this->qr_expires_at &&
            $this->qr_expires_at->isFuture();
    }

    // Gera o token automaticamente (podes chamar isto no momento da reserva)
    public function generateQrToken(): void
    {
        $this->qr_token = Str::uuid()->toString();
        $this->qr_expires_at = now()->addMinutes(15); // Exemplo: 15 mins de validade
    }
}
