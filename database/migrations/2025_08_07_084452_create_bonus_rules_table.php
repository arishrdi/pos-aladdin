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
        Schema::create('bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama aturan bonus
            $table->text('description')->nullable(); // Deskripsi aturan
            $table->enum('type', ['automatic', 'manual'])->default('manual'); // Tipe bonus
            $table->enum('trigger_type', ['minimum_purchase', 'product_quantity', 'member_type', 'category_purchase'])->nullable(); // Pemicu bonus
            $table->decimal('trigger_value', 15, 2)->nullable(); // Nilai pemicu (min purchase amount, qty, etc)
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade'); // Produk yang dapat bonus (null = semua produk)
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade'); // Kategori yang dapat bonus 
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade'); // Outlet spesifik (null = semua outlet)
            $table->enum('bonus_type', ['product', 'discount_percentage', 'discount_amount'])->default('product'); // Jenis bonus
            $table->foreignId('bonus_product_id')->nullable()->constrained('products')->onDelete('cascade'); // Produk bonus yang diberikan
            $table->decimal('bonus_quantity', 8, 2)->default(1); // Quantity bonus yang diberikan
            $table->decimal('bonus_value', 15, 2)->nullable(); // Nilai diskon (jika bonus berupa diskon)
            $table->decimal('max_bonus_per_transaction', 15, 2)->nullable(); // Maksimal bonus per transaksi
            $table->integer('max_usage_per_member')->nullable(); // Maksimal penggunaan per member
            $table->date('valid_from')->nullable(); // Tanggal berlaku mulai
            $table->date('valid_until')->nullable(); // Tanggal berlaku sampai
            $table->boolean('requires_approval')->default(false); // Memerlukan approval atau tidak
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('conditions')->nullable(); // Kondisi tambahan (JSON format)
            $table->timestamps();

            // Indexes untuk performance
            $table->index(['outlet_id', 'is_active']);
            $table->index(['type', 'trigger_type']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_rules');
    }
};