<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the roles table.
     * Roles use RBAC pattern — stored in table instead of enum
     * so they can be extended without changing the schema.
     *
     * Values: Administrateur | Technicien | Manager | Employé
     *
     * Raw SQL equivalent:
     * CREATE TABLE roles (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   name VARCHAR(50) NOT NULL UNIQUE,
     *   description TEXT NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};