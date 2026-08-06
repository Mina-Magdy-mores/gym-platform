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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_wallet_id')->constrained('trainer_wallets')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->decimal('amount', 10, 2); // Gross session amount (e.g. 500.00)
            $table->decimal('commission_amount', 10, 2); // 15% Gym commission (e.g. 75.00)
            $table->decimal('net_amount', 10, 2); // 85% Net credited to trainer (e.g. 425.00)
            $table->string('type')->default('session_credit'); // session_credit, payout_withdrawal
            $table->string('status')->default('completed'); // completed, pending, cancelled
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};