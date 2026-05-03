<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'brand',
    'model',
    'description',
])]
class Product extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function hardware(): HasOne
    {
        return $this->hasOne(Hardware::class);
    }

    public function software(): HasOne
    {
        return $this->hasOne(Software::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function assignmentDetails(): HasMany
    {
        return $this->hasMany(AssignmentDetail::class);
    }

    // Helper — returns 'hardware', 'software', or 'unknown'
    public function getTypeAttribute(): string
    {
        if ($this->hardware) return 'hardware';
        if ($this->software) return 'software';
        return 'unknown';
    }
}