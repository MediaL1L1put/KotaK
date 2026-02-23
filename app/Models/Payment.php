<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'amount',
        'payment_id',
        'status',
        'paid',
        'test',
        'metadata',
        'paid_at',
        'type', // 'ticket' или 'booking'
        'booking_id',
        'ticket_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid' => 'boolean',
        'test' => 'boolean',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function formattedAmount()
    {
        return number_format($this->amount, 0, ',', ' ') . ' ₽';
    }

    public function displayStatus()
    {
        $statuses = [
            'pending' => 'Ожидает оплаты',
            'waiting_for_capture' => 'Ожидает подтверждения',
            'succeeded' => 'Оплачено',
            'canceled' => 'Отменено',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}