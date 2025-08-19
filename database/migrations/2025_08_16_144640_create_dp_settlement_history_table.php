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
        Schema::create('dp_settlement_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->decimal('amount', 10, 2); // Jumlah pelunasan
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->string('payment_proof')->nullable(); // Path file bukti pembayaran
            $table->text('notes')->nullable(); // Catatan pelunasan
            $table->decimal('remaining_balance_before', 10, 2); // Sisa bayar sebelum pelunasan
            $table->decimal('remaining_balance_after', 10, 2); // Sisa bayar setelah pelunasan
            $table->boolean('is_final_payment')->default(false); // Apakah ini pelunasan terakhir
            $table->foreignId('processed_by')->constrained('users'); // User yang memproses
            $table->timestamp('processed_at'); // Waktu pemrosesan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dp_settlement_history');
    }
};
