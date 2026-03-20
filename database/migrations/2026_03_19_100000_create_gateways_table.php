<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // razorpay, stripe
            $table->string('display_name');            // Razorpay, Stripe
            $table->enum('type', ['payment', 'sms'])->default('payment');
            $table->enum('mode', ['test', 'live'])->default('test');

            $table->text('test_key')->nullable();
            $table->text('test_secret')->nullable();

            $table->text('live_key')->nullable();
            $table->text('live_secret')->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
