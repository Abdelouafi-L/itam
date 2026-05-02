<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the categories table.
     * Categories classify products — Ordinateur, Imprimante,
     * Logiciel, Périphérique, etc.
     * No timestamps — reference data that rarely changes.
     *
     * Raw SQL equivalent:
     * CREATE TABLE categories (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   name VARCHAR(100) NOT NULL UNIQUE,
     *   description TEXT NULL
     * );
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};