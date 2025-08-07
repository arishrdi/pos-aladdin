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
        Schema::create('bonus_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('bonus_number')->unique(); // Nomor unik bonus transaction
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade'); // Order yang memicu bonus (null jika manual)
            $table->foreignId('bonus_rule_id')->nullable()->constrained('bonus_rules')->onDelete('set null'); // Aturan bonus yang digunakan
            $table->foreignId('outlet_id')->constrained('outlets'); // Outlet yang memberikan bonus
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null'); // Member yang mendapat bonus (null jika guest)
            $table->foreignId('cashier_id')->constrained('users'); // Kasir yang memproses
            $table->foreignId('authorized_by')->nullable()->constrained('users')->onDelete('set null'); // User yang authorize (untuk manual bonus)
            $table->enum('type', ['automatic', 'manual'])->default('manual'); // Tipe bonus
            $table->enum('status', ['pending', 'approved', 'rejected', 'used'])->default('pending'); // Status bonus
            $table->decimal('total_value', 15, 2)->default(0); // Total nilai bonus yang diberikan
            $table->integer('total_items')->default(0); // Total item bonus
            $table->text('reason')->nullable(); // Alasan pemberian bonus (untuk manual)
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->json('conditions_met')->nullable(); // Kondisi yang terpenuhi (JSON)
            $table->timestamp('approved_at')->nullable(); // Waktu approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // User yang approve
            $table->text('approval_notes')->nullable(); // Catatan approval/rejection
            $table->timestamp('used_at')->nullable(); // Waktu bonus digunakan/diklaim
            $table->timestamp('expired_at')->nullable(); // Waktu kadaluarsa bonus
            $table->timestamps();

            // Indexes untuk performance
            $table->index(['outlet_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index(['cashier_id', 'created_at']);
            $table->index(['order_id']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_transactions');
    }
};