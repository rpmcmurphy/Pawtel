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
            $table->decimal('price', 10, 2);
            $table->integer('post_care_days')->default(0);
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
