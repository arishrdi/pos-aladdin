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
        Schema::table('products', function (Blueprint $table) {
            // Update enum to include 'pasang' and 'kirim'
            $table->enum('unit_type', ['meter', 'pcs', 'unit', 'pasang', 'kirim'])->default('meter')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('unit_type', ['meter', 'pcs', 'unit'])->default('meter')->change();
        });
    }
};
