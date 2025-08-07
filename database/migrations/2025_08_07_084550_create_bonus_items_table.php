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
        Schema::create('bonus_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_transaction_id')->constrained('bonus_transactions')->onDelete('cascade'); // Parent bonus transaction
            $table->foreignId('product_id')->constrained('products'); // Produk bonus yang diberikan
            $table->decimal('quantity', 8, 2); // Quantity bonus yang diberikan
            $table->decimal('product_price', 10, 2); // Harga produk saat bonus diberikan (untuk referensi nilai)
            $table->decimal('bonus_value', 15, 2); // Nilai bonus item ini (quantity * product_price)
            $table->text('notes')->nullable(); // Catatan khusus untuk item ini
            $table->enum('status', ['pending', 'approved', 'rejected', 'used'])->default('pending'); // Status item bonus
            $table->json('metadata')->nullable(); // Data tambahan dalam format JSON
            $table->timestamps();

            // Indexes untuk performance
            $table->index(['bonus_transaction_id', 'product_id']);
            $table->index(['product_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_items');
    }
};