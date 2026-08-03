<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for gym schedules.
     */
    public function up(): void
    {
        Schema::create('gym_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('target_gender', ['men', 'women']); // Target gender: men or women
            $table->string('days_label'); // Days group e.g. "السبت، الاثنين، والأربعاء"
            $table->time('start_time')->nullable(); // Opening time e.g. 08:00:00
            $table->time('end_time')->nullable(); // Closing time e.g. 13:00:00
            $table->string('time_label')->nullable(); // Time display text e.g. "8:00 ص - 1:00 م"
            $table->boolean('is_off_day')->default(false); // True for off days (e.g. Monday for Ladies)
            $table->string('notes')->nullable(); // Extra notes e.g. "يوم الاثنين اجازة للسيدات"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_schedules');
    }
};
