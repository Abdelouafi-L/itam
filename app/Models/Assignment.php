<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'employee_id',
    'created_by',
    'assigned_at',
    'returned_at',
    'status',
    'notes',
])]
class Assignment extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'assigned_at'  => 'datetime',
            'returned_at'  => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AssignmentDetail::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'Clôturée';
    }
}