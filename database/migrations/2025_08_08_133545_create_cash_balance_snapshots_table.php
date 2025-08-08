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
        Schema::create('cash_balance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->date('date');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('total_sales_cash', 15, 2)->default(0);
            $table->decimal('total_sales_other', 15, 2)->default(0);
            $table->decimal('manual_additions', 15, 2)->default(0);
            $table->decimal('manual_subtractions', 15, 2)->default(0);
            $table->decimal('refunds', 15, 2)->default(0);
            $table->integer('transactions_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Unique constraint untuk outlet per date
            $table->unique(['outlet_id', 'date'], 'unique_outlet_date');
            
            // Index untuk performance
            $table->index(['outlet_id', 'date'], 'idx_outlet_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_balance_snapshots');
    }
};
