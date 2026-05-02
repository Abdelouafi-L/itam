<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the maintenances table.
     * Maintenance records repair and service history for Hardware only.
     * Linked to Hardware (what was repaired) and User (who did it).
     *
     * When a maintenance record is created:
     * → Hardware condition may change
     * → A Tâche may be generated to track the work
     *
     * Raw SQL equivalent:
     * CREATE TABLE maintenances (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   hardware_id BIGINT UNSIGNED NOT NULL,
     *   technician_id BIGINT UNSIGNED NOT NULL,
     *   type VARCHAR(100) NOT NULL,
     *   description TEXT NULL,
     *   date DATE NOT NULL,
     *   cost DECIMAL(10,2) NULL,
     *   status ENUM('Planifiée','En cours','Terminée') NOT NULL DEFAULT 'Planifiée',
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();

            // The hardware being maintained — software cannot be maintained
            $table->foreignId('hardware_id')
                  ->constrained('hardware')
                  ->restrictOnDelete();

            // The technician responsible — named clearly, not just user_id
            $table->foreignId('technician_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // type: Préventive | Corrective | Mise à niveau | Nettoyage
            $table->string('type', 100);

            $table->text('description')->nullable();

            // date of the maintenance intervention
            $table->date('date');

            $table->decimal('cost', 10, 2)->nullable();

            $table->enum('status', ['Planifiée', 'En cours', 'Terminée'])
                  ->default('Planifiée');

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};