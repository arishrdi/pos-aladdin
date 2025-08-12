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
            // Tax type selection (PKP or NonPKP)
            $table->enum('tax_type', ['pkp', 'non_pkp'])->default('non_pkp')->after('tax');
            
            // PKP Banking Information
            $table->string('pkp_atas_nama_bank')->nullable()->after('tax_type');
            $table->string('pkp_nama_bank')->nullable()->after('pkp_atas_nama_bank');
            $table->string('pkp_nomor_transaksi_bank')->nullable()->after('pkp_nama_bank');
            // $table->string('pkp_qris')->nullable()->after('pkp_nomor_transaksi_bank');
            
            // NonPKP Banking Information
            $table->string('non_pkp_atas_nama_bank')->nullable()->after('pkp_nama_bank');
            $table->string('non_pkp_nama_bank')->nullable()->after('non_pkp_atas_nama_bank');
            $table->string('non_pkp_nomor_transaksi_bank')->nullable()->after('non_pkp_nama_bank');
            // $table->string('non_pkp_qris')->nullable()->after('non_pkp_nomor_transaksi_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn([
                'tax_type',
                'pkp_atas_nama_bank',
                'pkp_nama_bank', 
                'pkp_nomor_transaksi_bank',
                // 'pkp_qris',
                'non_pkp_atas_nama_bank',
                'non_pkp_nama_bank',
                'non_pkp_nomor_transaksi_bank',
                // 'non_pkp_qris'
            ]);
        });
    }
};
