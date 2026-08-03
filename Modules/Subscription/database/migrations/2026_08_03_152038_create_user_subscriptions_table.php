<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for user active subscriptions.
     */
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled', 'frozen'])->default('active');
            $table->decimal('price_paid', 10, 2);

            // Remaining benefits tracker
            $table->integer('remaining_freeze_days')->default(0);
            $table->integer('remaining_invitations')->default(0);
            $table->integer('remaining_inbody_scans')->default(0);
            $table->integer('remaining_pt_sessions')->default(0);
            $table->integer('remaining_kickboxing_classes')->default(0);
            $table->integer('remaining_nutrition_plans')->default(0);
            $table->timestamps();

            // Compound Index for ultra-fast subscription status verification
            $table->index(['user_id', 'status', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
