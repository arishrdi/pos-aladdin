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
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->nullable()->after('gender');
            $table->unsignedBigInteger('lead_id')->nullable()->after('outlet_id');
            $table->string('lead_number')->nullable()->after('lead_id');
            
            $table->index('lead_id');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['lead_id']);
            $table->dropIndex(['phone']);
            $table->dropColumn(['outlet_id', 'lead_id', 'lead_number']);
        });
    }
};
