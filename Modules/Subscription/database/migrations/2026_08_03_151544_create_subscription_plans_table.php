<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for subscription plans.
     */
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Plan Name e.g. 3 Month Offer
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('duration_months'); // Duration in months e.g. 3, 6, 14, 15
            $table->decimal('price', 10, 2); // Price in EGP e.g. 1750.00
            $table->string('currency')->default('EGP');
            $table->integer('free_days')->default(0); // Free days extension e.g. 15, 30
            $table->integer('freeze_days')->default(0); // Freeze allowance days e.g. 15, 30
            $table->integer('invitations_count')->default(0); // Guest invitations e.g. 10, 20
            $table->integer('inbody_scans')->default(0); // InBody scans count e.g. 3, 6
            $table->integer('pt_sessions')->default(0); // Free PT sessions e.g. 3, 4
            $table->integer('kickboxing_classes')->default(0); // Kickboxing classes e.g. 2, 3
            $table->integer('nutrition_plans')->default(0); // Customized nutrition plans e.g. 1, 2
            $table->boolean('spa_access')->default(true); // Unlimited SPA access
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active'); // Index for fast fetching of active plans

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
