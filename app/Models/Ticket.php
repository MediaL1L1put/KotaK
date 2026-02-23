<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'amount',
        'payment_id',
        'payment_status',
        'is_used',
        'used_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_used' => 'boolean',
        'used_at' => 'datetime'
    ];

    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now()
        ]);
    }
}