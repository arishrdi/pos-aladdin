<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_histories', function (Blueprint $table) {
            // Update the enum to include 'refund' type
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'other', 'stocktake', 'shipment', 'transfer_in', 'transfer_out', 'refund'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_histories', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'other', 'stocktake', 'shipment', 'transfer_in', 'transfer_out'])->change();
        });
    }
};