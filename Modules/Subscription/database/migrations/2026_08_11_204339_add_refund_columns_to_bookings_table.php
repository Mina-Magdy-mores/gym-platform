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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('refund_status')->default('none')->after('status'); // none, pending, refunded, rejected
            $table->string('refund_method')->nullable()->after('refund_status'); // auto_gateway, instapay, vodafone_cash, in_gym_cash
            $table->decimal('refunded_amount', 10, 2)->default(0.00)->after('refund_method');
            $table->timestamp('refunded_at')->nullable()->after('refunded_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['refund_status', 'refund_method', 'refunded_amount', 'refunded_at']);
        });
    }
};