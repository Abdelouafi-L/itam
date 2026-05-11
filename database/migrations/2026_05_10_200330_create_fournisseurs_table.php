<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the fournisseurs table.
     * Fournisseur = supplier master data.
     * Every delivery (Livraison) comes from a Fournisseur.
     *
     * Raw SQL equivalent:
     * CREATE TABLE fournisseurs (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   nom VARCHAR(255) NOT NULL,
     *   email VARCHAR(255) NULL,
     *   telephone VARCHAR(20) NULL,
     *   adresse TEXT NULL,
     *   contact_nom VARCHAR(100) NULL,
     *   site_web VARCHAR(255) NULL,
     *   numero_tva VARCHAR(50) NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();

            $table->string('nom');
            $table->string('email')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();

            // Primary contact person at the supplier
            $table->string('contact_nom', 100)->nullable();

            // Optional fields
            $table->string('site_web')->nullable();
            $table->string('numero_tva', 50)->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};