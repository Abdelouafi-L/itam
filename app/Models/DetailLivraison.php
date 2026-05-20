<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'livraison_id',
    'product_id',
    'quantite',
    'quantity_received',
    'prix_unitaire',
    'notes',
])]
class DetailLivraison extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'decimal:2',
            'created_at'    => 'datetime',
        ];
    }

    public function livraison(): BelongsTo
    {
        return $this->belongsTo(Livraison::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Total cost for this line
    public function getTotalAttribute(): float
    {
        return ($this->prix_unitaire ?? 0) * $this->quantite;
    }
}