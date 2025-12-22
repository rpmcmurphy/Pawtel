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
        Schema::create('spay_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // basic/premium/extended
            $table->enum('type', ['spay', 'neuter']);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Default/visitor price
            $table->decimal('resident_price', 10, 2)->nullable(); // Price for in-house guests (discounted)
            $table->integer('post_care_days')->default(0);
            $table->decimal('post_care_rate_first_3_days', 10, 2)->nullable(); // Per day rate for first 3 days (e.g., 1000 BDT)
            $table->decimal('post_care_rate_next_4_days', 10, 2)->nullable(); // Per day rate for next 4 days (e.g., 800 BDT)
            $table->decimal('post_care_rate_second_week', 10, 2)->nullable(); // Per day rate for second week (e.g., 600 BDT)
            $table->integer('max_daily_slots')->default(3);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spay_packages');
    }
};
