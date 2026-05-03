<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'assignment_id',
    'product_id',
    'quantity',
    'returned_qty',
    'serial_number',
    'asset_tag',
    'notes',
])]
class AssignmentDetail extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Quantity still out — not yet returned.
     */
    public function getQuantityOutAttribute(): int
    {
        return $this->quantity - $this->returned_qty;
    }

    /**
     * Is this detail fully returned?
     */
    public function isFullyReturned(): bool
    {
        return $this->returned_qty >= $this->quantity;
    }
}