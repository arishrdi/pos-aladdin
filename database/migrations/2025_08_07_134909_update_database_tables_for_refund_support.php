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
        // Update inventory_histories table to include 'refund' type
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'other', 'stocktake', 'shipment', 'transfer_in', 'transfer_out', 'refund'])->change();
        });

        // Update cash_register_transactions table to include 'refund' source
        Schema::table('cash_register_transactions', function (Blueprint $table) {
            $table->enum('source', ['cash', 'bank', 'pos', 'other', 'refund'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert inventory_histories table
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'other', 'stocktake', 'shipment', 'transfer_in', 'transfer_out'])->change();
        });

        // Revert cash_register_transactions table
        Schema::table('cash_register_transactions', function (Blueprint $table) {
            $table->enum('source', ['cash', 'bank', 'pos', 'other'])->change();
        });
    }
};