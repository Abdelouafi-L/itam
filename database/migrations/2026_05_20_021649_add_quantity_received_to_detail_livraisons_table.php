<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add quantity_received to detail_livraisons table.
     *
     * WHY: RF-36 defines partial reception — but the original schema
     * only stored the ordered quantity (quantite). There was no way
     * to track how many units were actually received per line item.
     *
     * quantity_received tracks what physically arrived in the warehouse.
     * pending = quantite - quantity_received (always calculated, never stored)
     *
     * Default 0 = nothing received yet when delivery is first created.
     */
    public function up(): void
    {
        Schema::table('detail_livraisons', function (Blueprint $table) {
            // Quantity actually received — updated on each reception event
            $table->integer('quantity_received')
                  ->default(0)
                  ->after('quantite');
        });
    }

    /**
     * Reverse the migration — remove the column.
     */
    public function down(): void
    {
        Schema::table('detail_livraisons', function (Blueprint $table) {
            $table->dropColumn('quantity_received');
        });
    }
};