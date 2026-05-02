<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the hardware table.
     * Hardware IS A Product — joined table inheritance pattern.
     * This table stores ONLY physical device specific data.
     * All commercial identity (name, brand, model) lives in products.
     *
     * NOTE: serial_number and asset_tag are NOT here.
     * They live in assignment_details because they are recorded
     * at assignment time, not at registration time.
     *
     * Example: "Dell Latitude 5540" product has one hardware record
     * describing its warranty and condition TYPE — not individual units.
     *
     * Raw SQL equivalent:
     * CREATE TABLE hardware (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   product_id BIGINT UNSIGNED NOT NULL UNIQUE,
     *   warranty_date DATE NULL,
     *   condition VARCHAR(50) NOT NULL DEFAULT 'Neuf',
     *   purchase_date DATE NULL
     * );
     */
    public function up(): void
    {
        Schema::create('hardware', function (Blueprint $table) {
            $table->id();

            // Inheritance link — one hardware maps to exactly one product
            $table->foreignId('product_id')
                  ->unique()
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->date('warranty_date')->nullable();

            // Condition tracks the physical state of this hardware type
            // Values: Neuf | Bon | Usagé | Endommagé
            $table->string('condition', 50)->default('Neuf');

            $table->date('purchase_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware');
    }
};