<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the employees table.
     * Employee is the base person record — may or may not have a system account.
     * Every User IS AN Employee, but not every Employee is a User.
     *
     * Raw SQL equivalent:
     * CREATE TABLE employees (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   department_id BIGINT UNSIGNED NOT NULL,
     *   first_name VARCHAR(50) NOT NULL,
     *   last_name VARCHAR(50) NOT NULL,
     *   email VARCHAR(255) NOT NULL UNIQUE,
     *   phone VARCHAR(20) NULL,
     *   hire_date DATE NULL,
     *   is_active TINYINT(1) NOT NULL DEFAULT 1,
     *   FOREIGN KEY (department_id) REFERENCES departments(id)
     * );
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Foreign key — employee belongs to one department
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->restrictOnDelete();

            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};