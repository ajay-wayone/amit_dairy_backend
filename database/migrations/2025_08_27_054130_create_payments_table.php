<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('transaction_id')->nullable();
        $table->string('payment_method')->default('stripe'); // stripe/razorpay/paypal
        $table->decimal('amount', 10, 2);
        $table->string('status')->default('pending'); // pending, succeeded, failed
        $table->longText('response')->nullable();
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
