<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkateModel extends Model
{
    protected $fillable = ['name', 'brand', 'description', 'image'];

    public function skates(): HasMany
    {
        return $this->hasMany(Skate::class);
    }
}