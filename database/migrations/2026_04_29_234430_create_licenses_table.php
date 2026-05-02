<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the licenses table.
     * License is linked to Software in a 1-to-1 relationship.
     * It represents the legal contract for a specific software product.
     *
     * This table drives RF-17 to RF-20:
     * - Track seats used vs total
     * - Alert when seats = 0
     * - Auto email when expiry <= 30 days
     *
     * Raw SQL equivalent:
     * CREATE TABLE licenses (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   software_id BIGINT UNSIGNED NOT NULL UNIQUE,
     *   seats_total INT UNSIGNED NOT NULL DEFAULT 1,
     *   seats_used INT UNSIGNED NOT NULL DEFAULT 0,
     *   purchase_date DATE NULL,
     *   expiry_date DATE NULL,
     *   cost DECIMAL(10,2) NULL,
     *   status ENUM('Active','Expirée','Résiliée') NOT NULL DEFAULT 'Active'
     * );
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            // 1-to-1 with software — one license per software product
            $table->foreignId('software_id')
                  ->unique()
                  ->constrained('software')
                  ->cascadeOnDelete();

            $table->unsignedInteger('seats_total')->default(1);
            $table->unsignedInteger('seats_used')->default(0);
            $table->date('purchase_date')->nullable();

            // expiry_date drives the RF-20 automatic notification
            $table->date('expiry_date')->nullable();

            // cost — 10 digits total, 2 decimal places
            // enough for 99,999,999.99 MAD
            $table->decimal('cost', 10, 2)->nullable();

            // status enum — exactly 3 values from your diagram
            $table->enum('status', ['Active', 'Expirée', 'Résiliée'])
                  ->default('Active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};