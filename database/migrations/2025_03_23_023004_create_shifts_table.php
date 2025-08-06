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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('user_id')->constrained('users');
            $table->time('start_time');
            $table->time('end_time');
            // $table->decimal('starting_cash', 10, 2);
            // $table->decimal('ending_cash', 10, 2)->nullable();
            // $table->decimal('expected_cash', 10, 2)->nullable();
            // $table->decimal('cash_difference', 10, 2)->nullable();
            // $table->text('notes')->nullable();
            // $table->boolean('is_closed')->default(false);
            // $table->foreignId('closed_by')->nullable()->constrained('users');
            // $table->datetime('closing_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
