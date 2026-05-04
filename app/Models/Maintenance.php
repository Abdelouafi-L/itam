<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'hardware_id',
    'technician_id',
    'type',
    'description',
    'date',
    'cost',
    'status',
])]
class Maintenance extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'cost'       => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function hardware(): BelongsTo
    {
        return $this->belongsTo(Hardware::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === 'Terminée';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'En cours';
    }
}