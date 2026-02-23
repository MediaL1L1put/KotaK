<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skate extends Model
{
    protected $fillable = ['skate_model_id', 'size', 'quantity', 'available_quantity', 'price_per_hour'];

    protected $casts = [
        'price_per_hour' => 'decimal:2'
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(SkateModel::class, 'skate_model_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailable(): bool
    {
        return $this->available_quantity > 0;
    }

    public function decreaseQuantity(): bool
    {
        if ($this->available_quantity > 0) {
            $this->available_quantity--;
            return $this->save();
        }
        return false;
    }

    public function increaseQuantity(): bool
    {
        if ($this->available_quantity < $this->quantity) {
            $this->available_quantity++;
            return $this->save();
        }
        return false;
    }

    public function reserve(int $hours = 1): bool
    {
        if ($this->decreaseQuantity()) {
            // Можно добавить логику временного резервирования
            return true;
        }
        return false;
    }

    public function release(): bool
    {
        return $this->increaseQuantity();
    }

    public function getInUseAttribute(): int
    {
        return $this->quantity - $this->available_quantity;
    }
}