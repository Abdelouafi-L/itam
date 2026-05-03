<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

#[Fillable([
    'software_id',
    'seats_total',
    'seats_used',
    'purchase_date',
    'expiry_date',
    'cost',
    'status',
])]
class License extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'expiry_date'   => 'date',
            'cost'          => 'decimal:2',
        ];
    }

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers — RF-18, RF-19, RF-20
    |--------------------------------------------------------------------------
    */

    /**
     * Days remaining until expiry.
     * RF-19: View license status with days remaining.
     * Returns null if no expiry date set.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->expiry_date) return null;
        return now()->startOfDay()->diffInDays(
            $this->expiry_date,
            false
        );
    }

    /**
     * Seats available — RF-18.
     */
    public function getSeatsAvailableAttribute(): int
    {
        return $this->seats_total - $this->seats_used;
    }

    /**
     * Is the license expiring within 30 days?
     * RF-20: Drives the automatic email notification.
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->days_remaining !== null
               && $this->days_remaining <= 30
               && $this->days_remaining >= 0;
    }

    /**
     * Is the license out of seats?
     * RF-18: Alert if seats = 0.
     */
    public function isOutOfSeats(): bool
    {
        return $this->seats_available <= 0;
    }
}