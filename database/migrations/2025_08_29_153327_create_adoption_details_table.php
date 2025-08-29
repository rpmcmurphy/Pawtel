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
        Schema::create('adoption_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('cat_name');
            $table->string('age')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->nullable();
            $table->string('breed')->nullable();
            $table->text('health_status')->nullable();
            $table->decimal('adoption_fee', 10, 2)->nullable();
            $table->json('contact_info')->nullable();
            $table->enum('status', ['available', 'pending', 'adopted'])->default('available');
            $table->timestamps();

            $table->index(['post_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_details');
    }
};
