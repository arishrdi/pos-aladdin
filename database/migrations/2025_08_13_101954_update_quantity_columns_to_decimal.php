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
        // Update order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change(); // Allows up to 9999999.999
        });

        // Update inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
            $table->decimal('min_stock', 10, 3)->default(0)->change();
        });

        // Update inventory_histories table
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->decimal('quantity_before', 10, 3)->change();
            $table->decimal('quantity_after', 10, 3)->change();
            $table->decimal('quantity_change', 10, 3)->change();
        });

        // Update bonus_items table if it exists and has quantity column
        if (Schema::hasTable('bonus_items') && Schema::hasColumn('bonus_items', 'quantity')) {
            Schema::table('bonus_items', function (Blueprint $table) {
                $table->decimal('quantity', 10, 3)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        // Rollback inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('min_stock')->default(0)->change();
        });

        // Rollback inventory_histories table
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->integer('quantity_before')->change();
            $table->integer('quantity_after')->change();
            $table->integer('quantity_change')->change();
        });

        // Rollback bonus_items table if it exists and has quantity column
        if (Schema::hasTable('bonus_items') && Schema::hasColumn('bonus_items', 'quantity')) {
            Schema::table('bonus_items', function (Blueprint $table) {
                $table->integer('quantity')->change();
            });
        }
    }
};
