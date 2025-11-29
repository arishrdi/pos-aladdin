<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes to optimize member search queries
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Add indexes for search columns to improve query performance
            $table->index('name', 'idx_members_name');
            $table->index('phone', 'idx_members_phone');
            $table->index('member_code', 'idx_members_code');
            $table->index('outlet_id', 'idx_members_outlet');

            // Composite index for common search pattern (outlet + name/phone)
            $table->index(['outlet_id', 'name'], 'idx_members_outlet_name');
            $table->index(['outlet_id', 'phone'], 'idx_members_outlet_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex('idx_members_name');
            $table->dropIndex('idx_members_phone');
            $table->dropIndex('idx_members_code');
            $table->dropIndex('idx_members_outlet');
            $table->dropIndex('idx_members_outlet_name');
            $table->dropIndex('idx_members_outlet_phone');
        });
    }
};
