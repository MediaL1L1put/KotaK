<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'hours',
        'skate_id',
        'skate_size',
        'total_amount',
        'payment_id',
        'payment_status',
        'has_own_skates'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'has_own_skates' => 'boolean'
    ];

    public function skate(): BelongsTo
    {
        return $this->belongsTo(Skate::class);
    }

    public function getSkatesCostAttribute(): float
    {
        if ($this->has_own_skates || !$this->skate_id) {
            return 0;
        }
        return $this->hours * 150;
    }

    public function getEntranceCostAttribute(): float
    {
        return 300;
    }

    public function getTotalCostAttribute(): float
    {
        return $this->entrance_cost + $this->skates_cost;
    }
}