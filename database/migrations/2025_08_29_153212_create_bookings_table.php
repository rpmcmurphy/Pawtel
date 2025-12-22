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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('booking_number')->unique();
            $table->enum('type', ['hotel', 'spa', 'spay']);
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('total_days');
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->boolean('is_manual_entry')->default(false);
            $table->string('manual_reference')->nullable();
            $table->boolean('is_resident')->default(false); // Track if user is an in-house guest/resident
            $table->timestamps();

            $table->index(['booking_number', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['check_in_date', 'check_out_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
