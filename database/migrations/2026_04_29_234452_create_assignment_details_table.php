<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the assignment_details table.
     * AssignmentDetail is the line item record — like an invoice line.
     * One Assignment has one or more AssignmentDetails.
     *
     * KEY ARCHITECTURAL DECISION:
     * serial_number and asset_tag live HERE, not in products/hardware.
     * Reason: they are recorded at assignment time when you physically
     * handle the unit — before assignment you don't know which specific
     * unit goes to whom.
     *
     * serial_number → [0..1] optional, non-unique
     *   Different manufacturers can have same serial numbers.
     *   asset_tag is the internal unique identifier.
     *
     * asset_tag → [0..1] optional
     *   Generated internally at assignment time.
     *   Bulk items (USB drives) may have no asset tag.
     *
     * Raw SQL equivalent:
     * CREATE TABLE assignment_details (
     *   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     *   assignment_id BIGINT UNSIGNED NOT NULL,
     *   product_id BIGINT UNSIGNED NOT NULL,
     *   quantity INT UNSIGNED NOT NULL DEFAULT 1,
     *   returned_qty INT UNSIGNED NOT NULL DEFAULT 0,
     *   serial_number VARCHAR(100) NULL,
     *   asset_tag VARCHAR(100) NULL UNIQUE,
     *   notes TEXT NULL,
     *   created_at TIMESTAMP NULL
     * );
     */
    public function up(): void
    {
        Schema::create('assignment_details', function (Blueprint $table) {
            $table->id();

            // Parent assignment header
            $table->foreignId('assignment_id')
                  ->constrained('assignments')
                  ->cascadeOnDelete();

            // The product being assigned
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('returned_qty')->default(0);

            // serial_number — optional, NOT unique at DB level
            // Different manufacturers reuse serial numbers
            $table->string('serial_number', 100)->nullable();

            // asset_tag — optional but unique when present
            // Internal identifier generated at assignment time
            // uniqueNullable allows multiple NULL values
            // while enforcing uniqueness for non-null values
            $table->string('asset_tag', 100)->nullable()->unique();

            $table->text('notes')->nullable();

            // Only created_at — detail lines are never updated
            // A return creates a new record, not an update
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_details');
    }
};