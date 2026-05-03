<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'product_id',
    'version',
    'license_type',
    'platform',
    'publisher',
    'release_date',
])]
class Software extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function license(): HasOne
    {
        return $this->hasOne(License::class);
    }
}