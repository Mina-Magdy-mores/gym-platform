<?php

use Dom\Comment;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('gateway')->default('mock')->comment('mock, paymob, stripe'); // mock, paymob, stripe
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('completed')->comment('pending, completed, failed, refunded'); // pending, completed, failed, refunded
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};