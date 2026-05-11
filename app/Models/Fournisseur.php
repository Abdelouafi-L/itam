<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nom',
    'email',
    'telephone',
    'adresse',
    'contact_nom',
    'site_web',
    'numero_tva',
])]
class Fournisseur extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function livraisons(): HasMany
    {
        return $this->hasMany(Livraison::class);
    }

    // Total deliveries count helper
    public function getLivraisonsCountAttribute(): int
    {
        return $this->livraisons()->count();
    }
}