<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the products table.
     * Product is the parent class for Hardware and Software.
     * It describes the commercial identity of an item —
     * what it IS, not individual physical units.
     *
     * Example: "Dell Latitude 5540" is one Product record.
     * 50 physical units of it = 1 Product + 1 Stock record.
     *
     * Raw SQL equivalent:
     * CREATE TABLE products (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   category_id BIGINT UNSIGNED NOT NULL,
     *   name VARCHAR(255) NOT NULL,
     *   brand VARCHAR(100) NULL,
     *   model VARCHAR(100) NULL,
     *   description TEXT NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Foreign key — product belongs to one category
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();

            $table->string('name');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};