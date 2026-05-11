<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the livraisons table.
     * Livraison = delivery header — like an invoice header.
     *
     * Key fields Mr. Anwar requested:
     * - reference_interne: our internal tracking number (unique)
     * - bon_de_livraison: the supplier's delivery note number
     * - signataire_id: which Employee physically signed/accepted delivery
     * - statut lifecycle: En attente → Réceptionnée | Partielle | Annulée
     *
     * Raw SQL equivalent:
     * CREATE TABLE livraisons (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   fournisseur_id BIGINT UNSIGNED NOT NULL,
     *   signataire_id BIGINT UNSIGNED NOT NULL,
     *   reference_interne VARCHAR(100) NOT NULL UNIQUE,
     *   bon_de_livraison VARCHAR(100) NOT NULL,
     *   date_livraison DATE NOT NULL,
     *   statut ENUM(...) NOT NULL DEFAULT 'En attente',
     *   notes TEXT NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();

            // The supplier who sent this delivery
            $table->foreignId('fournisseur_id')
                  ->constrained('fournisseurs')
                  ->restrictOnDelete();

            // The employee who physically signed and accepted delivery
            // Mr. Anwar: "knowing which Employee signed the invoice"
            $table->foreignId('signataire_id')
                  ->constrained('employees')
                  ->restrictOnDelete();

            // Our internal reference — unique tracking number
            // e.g. LIV-2026-001
            $table->string('reference_interne', 100)->unique();

            // Supplier's delivery note / bon de livraison number
            // e.g. BL-SUP-78542
            $table->string('bon_de_livraison', 100);

            $table->date('date_livraison');

            // Lifecycle status — Mr. Anwar's lifecycle structure
            $table->enum('statut', [
                'En attente',   // Created, not yet received
                'Réceptionnée', // Fully received and validated
                'Partielle',    // Partially received
                'Annulée',      // Cancelled — no stock update
            ])->default('En attente');

            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};