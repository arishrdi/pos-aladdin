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
            $table->foreignId('finance_approved_by')->nullable()->constrained('users')->after('updated_at');
            $table->timestamp('finance_approved_at')->nullable()->after('finance_approved_by');
            $table->foreignId('operational_approved_by')->nullable()->constrained('users')->after('finance_approved_at');
            $table->timestamp('operational_approved_at')->nullable()->after('operational_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['finance_approved_by']);
            $table->dropColumn('finance_approved_by');
            $table->dropColumn('finance_approved_at');
            $table->dropForeign(['operational_approved_by']);
            $table->dropColumn('operational_approved_by');
            $table->dropColumn('operational_approved_at');
        });
    }
};
