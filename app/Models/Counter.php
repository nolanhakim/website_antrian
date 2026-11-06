<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'prefix', 'is_active'])]
class Counter extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
