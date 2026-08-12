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
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_routine_id')->constrained('workout_routines')->onDelete('cascade');
            $table->string('day_name')->default('Day 1');
            $table->string('exercise_name');
            $table->string('target_muscle')->nullable();
            $table->integer('sets')->default(3);
            $table->string('reps')->default('10-12');
            $table->integer('rest_seconds')->default(60);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routine_exercises');
    }
};
