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
        // Menambahkan cascade delete pada order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        // Menambahkan cascade delete pada inventory_history
        if (Schema::hasTable('inventory_history')) {
            Schema::table('inventory_history', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_history', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                }
            });
        }

        // Menambahkan cascade delete pada bonus_transactions
        if (Schema::hasTable('bonus_transactions')) {
            Schema::table('bonus_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('bonus_transactions', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                }
            });
        }

        // Menambahkan cascade delete pada dp_settlement_history
        if (Schema::hasTable('dp_settlement_history')) {
            Schema::table('dp_settlement_history', function (Blueprint $table) {
                if (Schema::hasColumn('dp_settlement_history', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan foreign key tanpa cascade delete pada order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->foreign('order_id')->references('id')->on('orders');
        });

        // Mengembalikan foreign key pada inventory_history
        if (Schema::hasTable('inventory_history')) {
            Schema::table('inventory_history', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_history', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders');
                }
            });
        }

        // Mengembalikan foreign key pada bonus_transactions
        if (Schema::hasTable('bonus_transactions')) {
            Schema::table('bonus_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('bonus_transactions', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders');
                }
            });
        }

        // Mengembalikan foreign key pada dp_settlement_history
        if (Schema::hasTable('dp_settlement_history')) {
            Schema::table('dp_settlement_history', function (Blueprint $table) {
                if (Schema::hasColumn('dp_settlement_history', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders');
                }
            });
        }
    }
};
