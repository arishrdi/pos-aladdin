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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_change')->nullable();
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'other', 'stocktake', 'shipment', 'transfer_in', 'transfer_out']);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved'); // atau default 'approved' untuk perubahan langsung
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
