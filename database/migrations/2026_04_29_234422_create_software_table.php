<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the software table.
     * Software IS A Product — joined table inheritance pattern.
     * This table stores ONLY software specific data.
     * All commercial identity (name, brand, model) lives in products.
     *
     * Raw SQL equivalent:
     * CREATE TABLE software (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   product_id BIGINT UNSIGNED NOT NULL UNIQUE,
     *   version VARCHAR(50) NULL,
     *   license_type VARCHAR(100) NULL,
     *   platform VARCHAR(100) NULL,
     *   publisher VARCHAR(100) NULL,
     *   release_date DATE NULL
     * );
     */
    public function up(): void
    {
        Schema::create('software', function (Blueprint $table) {
            $table->id();

            // Inheritance link — one software maps to exactly one product
            $table->foreignId('product_id')
                  ->unique()
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->string('version', 50)->nullable();

            // license_type: Perpétuelle | Abonnement | Open Source | Essai
            $table->string('license_type', 100)->nullable();

            // platform: Windows | Linux | macOS | Web | Cross-platform
            $table->string('platform', 100)->nullable();

            $table->string('publisher', 100)->nullable();
            $table->date('release_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software');
    }
};