<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'department_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'hire_date',
    'is_active',
])]
class Employee extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Employee has no updated_at — we track changes via assignments
     * and audit trails, not by modifying employee records directly.
     */
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * An Employee belongs to one Department.
     * Access via: $employee->department->name
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * An Employee may have one User account.
     * Not every employee has system access.
     * Access via: $employee->user
     * Check via: $employee->hasAccount()
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * An Employee has many Assignments.
     * Access via: $employee->assignments
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get full name — used in dropdowns and displays.
     * Usage: $employee->full_name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if this employee has a system user account.
     * Usage: $employee->hasAccount()
     */
    public function hasAccount(): bool
    {
        return $this->user()->exists();
    }

    /**
     * Scope — only active employees.
     * Usage: Employee::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}