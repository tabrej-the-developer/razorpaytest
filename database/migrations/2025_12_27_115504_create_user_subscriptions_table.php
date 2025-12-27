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
      Schema::create('user_subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('razorpay_subscription_id')->unique();
    $table->string('razorpay_payment_id')->nullable();
    $table->decimal('upfront_amount', 10, 2)->default(6.00);
    $table->decimal('monthly_amount', 10, 2)->default(4.00);
    $table->string('status')->default('active'); // active, cancelled, paused
    $table->timestamp('razorpay_current_start')->nullable();
    $table->timestamp('razorpay_current_end')->nullable();
    $table->timestamp('ends_at')->nullable();
    $table->timestamps();
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
