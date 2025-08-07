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
            // Request pembatalan/refund
            $table->enum('cancellation_status', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('approval_notes');
            $table->string('cancellation_reason')->nullable()->after('cancellation_status');
            $table->text('cancellation_notes')->nullable()->after('cancellation_reason');
            $table->unsignedBigInteger('cancellation_requested_by')->nullable()->after('cancellation_notes');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_requested_by');
            $table->unsignedBigInteger('cancellation_processed_by')->nullable()->after('cancellation_requested_at');
            $table->timestamp('cancellation_processed_at')->nullable()->after('cancellation_processed_by');
            $table->text('cancellation_admin_notes')->nullable()->after('cancellation_processed_at');

            // Foreign keys
            $table->foreign('cancellation_requested_by')->references('id')->on('users');
            $table->foreign('cancellation_processed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancellation_requested_by']);
            $table->dropForeign(['cancellation_processed_by']);
            $table->dropColumn([
                'cancellation_status',
                'cancellation_reason', 
                'cancellation_notes',
                'cancellation_requested_by',
                'cancellation_requested_at',
                'cancellation_processed_by',
                'cancellation_processed_at',
                'cancellation_admin_notes'
            ]);
        });
    }
};