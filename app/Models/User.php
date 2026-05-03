<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'employee_id',
    'role_id',
    'password',
    'is_active',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Tell Laravel auth system where to find the email for login.
     * Our email lives on the employees table, not users.
     * We override getAuthIdentifierName() via the employee relationship.
     */
    protected $table = 'users';

    // Our users table has only created_at — no updated_at
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'last_login' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A User IS AN Employee — joined table inheritance.
     * Access employee data via: $user->employee->first_name
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * A User has one Role.
     * Access role via: $user->role->name
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Auth Helpers — bridge between users and employees tables
    |--------------------------------------------------------------------------
    */

    /**
     * Get the email for authentication.
     * Laravel's Auth::attempt() looks for this.
     * We fetch it from the related employee record.
     */
    public function getEmailAttribute(): string
    {
        return $this->employee?->email ?? '';
    }

    /**
     * Get first name — convenience accessor.
     * Used in: "Bienvenue {{ Auth::user()->first_name }}"
     */
    public function getFirstNameAttribute(): string
    {
        return $this->employee?->first_name ?? '';
    }

    /**
     * Get last name — convenience accessor.
     */
    public function getLastNameAttribute(): string
    {
        return $this->employee?->last_name ?? '';
    }

    /**
     * Get full name — convenience accessor.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Override Laravel's default email lookup for Auth::attempt().
     * By default Laravel does: SELECT * FROM users WHERE email = ?
     * Our email is on employees table, so we override the query.
     *
     * Raw PHP equivalent: your manual PDO join query in Auth.php
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if user has a specific role by name.
     * Usage: $user->hasRole('Administrateur')
     */
    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrateur');
    }

    /**
     * Check if user is a technician.
     */
    public function isTechnicien(): bool
    {
        return $this->hasRole('Technicien');
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole('Manager');
    }

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

    /**
     * Send password reset notification.
     * Overrides Laravel's default reset email with our French version.
     * Raw PHP equivalent: your Mailer::sendResetEmail() method
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

}