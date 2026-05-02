<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the assignments table.
     * Assignment is the header record — like an invoice header.
     * It records WHO received assets and WHO created the assignment.
     *
     * Two distinct relationships to users/employees:
     * - employee_id → the person RECEIVING the assets
     * - created_by  → the User (technician/admin) who created the record
     *
     * Raw SQL equivalent:
     * CREATE TABLE assignments (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   employee_id BIGINT UNSIGNED NOT NULL,
     *   created_by BIGINT UNSIGNED NOT NULL,
     *   assigned_at TIMESTAMP NOT NULL,
     *   returned_at TIMESTAMP NULL,
     *   status ENUM('Active','Clôturée') NOT NULL DEFAULT 'Active',
     *   notes TEXT NULL
     * );
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            // The employee receiving the assets
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->restrictOnDelete();

            // The user (technician/admin) who created this assignment
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();

            $table->enum('status', ['Active', 'Clôturée'])
                  ->default('Active');

            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};