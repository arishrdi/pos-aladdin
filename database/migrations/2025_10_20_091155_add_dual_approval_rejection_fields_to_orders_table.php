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
            // Finance rejection fields
            $table->text('finance_rejection_reason')->nullable()->after('finance_approved_at');
            $table->foreignId('finance_rejected_by')->nullable()->constrained('users')->after('finance_rejection_reason');
            $table->timestamp('finance_rejected_at')->nullable()->after('finance_rejected_by');

            // Operational rejection fields
            $table->text('operational_rejection_reason')->nullable()->after('operational_approved_at');
            $table->foreignId('operational_rejected_by')->nullable()->constrained('users')->after('operational_rejection_reason');
            $table->timestamp('operational_rejected_at')->nullable()->after('operational_rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['finance_rejected_by']);
            $table->dropColumn(['finance_rejection_reason', 'finance_rejected_by', 'finance_rejected_at']);

            $table->dropForeign(['operational_rejected_by']);
            $table->dropColumn(['operational_rejection_reason', 'operational_rejected_by', 'operational_rejected_at']);
        });
    }
};
