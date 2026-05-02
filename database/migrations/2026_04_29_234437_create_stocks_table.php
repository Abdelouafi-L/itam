<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the stocks table.
     * Stock tracks quantity for a product — NOT individual units.
     *
     * The key architectural decision:
     * 1000 USB drives = 1 Product record + 1 Stock record
     * NOT 1000 individual asset records.
     *
     * quantity_total     = quantity_available + quantity_assigned
     * This invariant must always be maintained in application logic.
     *
     * Raw SQL equivalent:
     * CREATE TABLE stocks (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   product_id BIGINT UNSIGNED NOT NULL UNIQUE,
     *   quantity_total INT UNSIGNED NOT NULL DEFAULT 0,
     *   quantity_available INT UNSIGNED NOT NULL DEFAULT 0,
     *   quantity_assigned INT UNSIGNED NOT NULL DEFAULT 0,
     *   updated_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // 1-to-1 with product — every product has exactly one stock record
            $table->foreignId('product_id')
                  ->unique()
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->unsignedInteger('quantity_total')->default(0);
            $table->unsignedInteger('quantity_available')->default(0);
            $table->unsignedInteger('quantity_assigned')->default(0);

            // Only updated_at — we care when stock last changed, not when created
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};