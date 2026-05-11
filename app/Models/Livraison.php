<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'fournisseur_id',
    'signataire_id',
    'reference_interne',
    'bon_de_livraison',
    'date_livraison',
    'statut',
    'notes',
])]
class Livraison extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'date_livraison' => 'date',
            'created_at'     => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    // The employee who signed/accepted the delivery
    public function signataire(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signataire_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailLivraison::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers — Lifecycle
    |--------------------------------------------------------------------------
    */

    public function isEnAttente(): bool
    {
        return $this->statut === 'En attente';
    }

    public function isReceptionnee(): bool
    {
        return $this->statut === 'Réceptionnée';
    }

    public function isPartielle(): bool
    {
        return $this->statut === 'Partielle';
    }

    public function isAnnulee(): bool
    {
        return $this->statut === 'Annulée';
    }

    public function isClosed(): bool
    {
        return in_array($this->statut, ['Réceptionnée', 'Annulée']);
    }

    // Generate next internal reference — LIV-2026-001
    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)
                      ->orderByDesc('id')
                      ->first();

        $number = $last ? (int) substr($last->reference_interne, -3) + 1 : 1;

        return 'LIV-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}