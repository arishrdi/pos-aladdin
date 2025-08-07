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
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_bonus')->default(false)->after('subtotal'); // Menandai apakah item ini bonus
            $table->foreignId('bonus_transaction_id')->nullable()->constrained('bonus_transactions')->onDelete('set null')->after('is_bonus'); // Referensi ke bonus transaction
            $table->foreignId('bonus_rule_id')->nullable()->constrained('bonus_rules')->onDelete('set null')->after('bonus_transaction_id'); // Referensi ke bonus rule yang digunakan
            $table->decimal('original_price', 10, 2)->nullable()->after('bonus_rule_id'); // Harga asli produk (untuk referensi jika bonus)
            $table->text('bonus_notes')->nullable()->after('original_price'); // Catatan khusus untuk item bonus
            
            // Index untuk performance
            $table->index(['is_bonus', 'order_id']);
            $table->index(['bonus_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['is_bonus', 'order_id']);
            $table->dropIndex(['bonus_transaction_id']);
            
            $table->dropConstrainedForeignId('bonus_rule_id');
            $table->dropConstrainedForeignId('bonus_transaction_id');
            $table->dropColumn(['is_bonus', 'original_price', 'bonus_notes']);
        });
    }
};