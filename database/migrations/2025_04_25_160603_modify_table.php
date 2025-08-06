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
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('atas_nama_bank')->nullable();
            $table->string('nama_bank')->nullable();
            $table->integer('nomor_transaksi_bank')->nullable();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn('atas_nama_bank');
            $table->dropColumn('nama_bank');
            $table->dropColumn('nomor_transaksi_bank');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'qris'])->change();
        });
    }
};
