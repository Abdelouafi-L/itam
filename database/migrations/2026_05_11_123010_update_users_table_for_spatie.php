<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove role_id from users table.
     * Spatie manages user-role relationships via its own pivot table.
     * This column is no longer needed — Spatie's model_has_roles
     * pivot table handles the User → Role relationship.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['role_id']);
            // Then drop the column
            $table->dropColumn('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                  ->nullable()
                  ->after('employee_id');
        });
    }
};