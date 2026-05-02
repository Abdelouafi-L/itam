<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the users table.
     *
     * User IS AN Employee — joined table inheritance pattern.
     * This table stores ONLY login-specific data.
     * All person data (name, email) lives in employees table.
     *
     * Raw SQL equivalent:
     * CREATE TABLE users (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   employee_id BIGINT UNSIGNED NOT NULL UNIQUE,
     *   role_id BIGINT UNSIGNED NOT NULL,
     *   password VARCHAR(255) NOT NULL,
     *   last_login TIMESTAMP NULL,
     *   is_active TINYINT(1) NOT NULL DEFAULT 1,
     *   created_at TIMESTAMP NULL,
     *   remember_token VARCHAR(100) NULL
     * );
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Inheritance link — one user maps to exactly one employee
            // unique() enforces the 1-to-1 relationship at DB level
            $table->foreignId('employee_id')
                  ->unique()
                  ->constrained('employees')
                  ->cascadeOnDelete();

            // Role — every user has exactly one role
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->restrictOnDelete();

            $table->string('password');
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            // Required by Laravel for "remember me" functionality
            // Replaces your manual remember-me cookie logic
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};