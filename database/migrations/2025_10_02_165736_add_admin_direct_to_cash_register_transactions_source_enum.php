<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing 'refund' entries to 'other'
        DB::table('cash_register_transactions')
            ->where('source', 'refund')
            ->update(['source' => 'other']);
            
        // Now modify the enum to include 'admin_direct' and 'refund'
        DB::statement("ALTER TABLE cash_register_transactions MODIFY COLUMN source ENUM('cash', 'bank', 'pos', 'other', 'admin_direct', 'refund')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update any 'admin_direct' entries to 'other' before removing from enum
        DB::table('cash_register_transactions')
            ->where('source', 'admin_direct')
            ->update(['source' => 'other']);
            
        DB::statement("ALTER TABLE cash_register_transactions MODIFY COLUMN source ENUM('cash', 'bank', 'pos', 'other', 'refund')");
    }
};
