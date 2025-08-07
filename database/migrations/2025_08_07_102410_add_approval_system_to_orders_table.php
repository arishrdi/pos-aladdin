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
            // Approval system columns
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->string('payment_proof')->nullable()->after('payment_method'); // Path to uploaded payment proof
            $table->text('approval_notes')->nullable()->after('payment_proof'); // Notes from approver
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_notes'); // User who approved/rejected
            $table->timestamp('approved_at')->nullable()->after('approved_by'); // When approved/rejected
            $table->text('rejection_reason')->nullable()->after('approved_at'); // Reason for rejection
            
            // Add foreign key constraint for approver
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Add index for better query performance
            $table->index('approval_status');
            $table->index(['outlet_id', 'approval_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['approval_status']);
            $table->dropIndex(['outlet_id', 'approval_status']);
            $table->dropColumn([
                'approval_status',
                'payment_proof', 
                'approval_notes',
                'approved_by',
                'approved_at',
                'rejection_reason'
            ]);
        });
    }
};