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
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->date('report_date');
            $table->decimal('total_sales', 12, 2);
            $table->integer('total_transactions');
            $table->decimal('average_transaction', 10, 2);
            $table->foreignId('generated_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['outlet_id', 'report_date']);
        });

        Schema::create('monthly_inventory_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('product_id')->constrained('products');
            $table->date('report_date');
            $table->integer('opening_stock');
            $table->integer('closing_stock');
            $table->integer('sales_quantity');
            $table->integer('purchase_quantity');
            $table->integer('adjustment_quantity');
            $table->decimal('stock_value', 12, 2);
            $table->timestamps();
            
            // $table->unique(['outlet_id', 'product_id', 'report_date']);
            $table->unique(['outlet_id', 'product_id', 'report_date'], 'm_inv_rep_out_prod_date_uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
        Schema::dropIfExists('monthly_inventory_reports');
    }
};
