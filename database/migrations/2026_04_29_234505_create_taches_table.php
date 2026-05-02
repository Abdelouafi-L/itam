<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the taches table.
     * Tâche represents a work order — it can be linked to:
     * - An Assignment (asset delivery task)
     * - A Maintenance (repair task)
     * - Neither (standalone administrative task)
     *
     * Both assignment_id and maintenance_id are optional [0..1]
     * A task can exist independently of both.
     *
     * Raw SQL equivalent:
     * CREATE TABLE taches (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   assigned_to BIGINT UNSIGNED NOT NULL,
     *   assignment_id BIGINT UNSIGNED NULL,
     *   maintenance_id BIGINT UNSIGNED NULL,
     *   title VARCHAR(255) NOT NULL,
     *   description TEXT NULL,
     *   status ENUM('Planifiée','En cours','Terminée','Annulée') NOT NULL DEFAULT 'Planifiée',
     *   priority ENUM('Basse','Normale','Haute','Urgente') NOT NULL DEFAULT 'Normale',
     *   due_date DATE NULL,
     *   completed_at TIMESTAMP NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();

            // The user this task is assigned to
            $table->foreignId('assigned_to')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Optional link to an assignment — nullOnDelete because
            // deleting an assignment should not delete its tasks
            // Tasks are historical records — preserve them
            $table->foreignId('assignment_id')
                  ->nullable()
                  ->constrained('assignments')
                  ->nullOnDelete();

            // Optional link to a maintenance record
            $table->foreignId('maintenance_id')
                  ->nullable()
                  ->constrained('maintenances')
                  ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('status', [
                'Planifiée',
                'En cours',
                'Terminée',
                'Annulée'
            ])->default('Planifiée');

            $table->enum('priority', [
                'Basse',
                'Normale',
                'Haute',
                'Urgente'
            ])->default('Normale');

            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};