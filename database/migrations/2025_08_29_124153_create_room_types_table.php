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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Single/Double/Family
            $table->string('slug')->unique();
            $table->decimal('base_daily_rate', 10, 2); // Per day rate (e.g., 500 BDT)
            $table->decimal('rate_7plus_days', 10, 2)->nullable(); // Per day rate for 7+ days stay (e.g., 450 BDT/day)
            $table->decimal('rate_10plus_days', 10, 2)->nullable(); // Per day rate for 10+ days stay (e.g., 400 BDT/day)
            $table->decimal('monthly_package_price', 10, 2)->nullable(); // Fixed monthly package price (e.g., 10000 BDT)
            $table->boolean('monthly_custom_discount_enabled')->default(false); // Allow admin to set custom discount for monthly stays
            $table->integer('max_capacity')->default(1);
            $table->json('amenities')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);

            $table->index(['slug', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
