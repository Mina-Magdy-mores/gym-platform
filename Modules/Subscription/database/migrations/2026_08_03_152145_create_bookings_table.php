<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for private trainer session bookings.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Member booking
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete(); // Trainer booked
            $table->date('booking_date'); // Session date
            $table->time('start_time'); // Start time e.g. 10:00:00
            $table->time('end_time'); // End time e.g. 11:00:00
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('confirmed');
            $table->decimal('price', 10, 2); // Session price
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound Index for fast queries and concurrency lock performance
            $table->index(['trainer_id', 'booking_date', 'start_time']);
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
