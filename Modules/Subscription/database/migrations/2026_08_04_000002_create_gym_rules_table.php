<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for gym rules & regulations table.
     */
    public function up(): void
    {
        Schema::create('gym_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('rule_number')->unique();
            $table->text('rule_text');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_rules');
    }
};
