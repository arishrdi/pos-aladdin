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
        Schema::create('transaction_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->enum('edit_type', ['quantity_adjustment', 'item_modification', 'item_addition', 'item_removal']);
            $table->json('original_data');
            $table->json('new_data');
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->decimal('total_difference', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Dual approval fields - konsisten dengan orders table
            $table->foreignId('finance_approved_by')->nullable()->constrained('users');
            $table->timestamp('finance_approved_at')->nullable();
            $table->foreignId('operational_approved_by')->nullable()->constrained('users');
            $table->timestamp('operational_approved_at')->nullable();
            
            // Rejection fields
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Applied timestamp
            $table->timestamp('applied_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_edits');
    }
};
