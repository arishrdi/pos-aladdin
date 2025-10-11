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
        Schema::table('orders', function (Blueprint $table) {
            // Add leads cabang and deal maker fields
            $table->foreignId('leads_cabang_outlet_id')->nullable()->constrained('outlets')->after('mosque_id');
            $table->foreignId('deal_maker_outlet_id')->nullable()->constrained('outlets')->after('leads_cabang_outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['leads_cabang_outlet_id']);
            $table->dropForeign(['deal_maker_outlet_id']);
            $table->dropColumn(['leads_cabang_outlet_id', 'deal_maker_outlet_id']);
        });
    }
};
