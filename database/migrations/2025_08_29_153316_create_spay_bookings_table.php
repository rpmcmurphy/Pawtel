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
        Schema::create('spay_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('spay_package_id')->constrained()->onDelete('cascade');
            $table->date('procedure_date');
            $table->string('pet_name');
            $table->string('pet_age')->nullable();
            $table->decimal('pet_weight', 5, 2)->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('vet_assigned')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['procedure_date', 'status']);
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spay_bookings');
    }
};
