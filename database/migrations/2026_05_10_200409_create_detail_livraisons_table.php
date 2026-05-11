<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the detail_livraisons table.
     * DetaillLivraison = delivery line items — like invoice lines.
     * One Livraison has one or more DetaillLivraison records.
     *
     * KEY RULE from Mr. Anwar:
     * When Livraison.statut → Réceptionnée (MANUAL trigger):
     *   Stock.quantity_total     += quantite
     *   Stock.quantity_available += quantite
     * For Partielle: only validated lines update stock.
     * For Annulée: NO stock update at all.
     *
     * Raw SQL equivalent:
     * CREATE TABLE detail_livraisons (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   livraison_id BIGINT UNSIGNED NOT NULL,
     *   product_id BIGINT UNSIGNED NOT NULL,
     *   quantite INT UNSIGNED NOT NULL,
     *   prix_unitaire DECIMAL(10,2) NULL,
     *   notes TEXT NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('detail_livraisons', function (Blueprint $table) {
            $table->id();

            // Parent delivery header
            $table->foreignId('livraison_id')
                  ->constrained('livraisons')
                  ->cascadeOnDelete();

            // The product that was delivered
            // Links to Product — identifies WHAT was delivered
            // Stock is updated as a consequence of this link
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            // Quantity delivered for this line
            $table->unsignedInteger('quantite');

            // Unit price at delivery time
            // Stored here because prices change — historical record
            $table->decimal('prix_unitaire', 10, 2)->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_livraisons');
    }
};