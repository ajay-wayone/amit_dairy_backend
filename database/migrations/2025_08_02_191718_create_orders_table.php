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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('order_id');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_state');
            $table->string('delivery_pincode');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cod', 'online', 'card'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('order_status', ['pending', 'confirmed', 'processing', 'ready', 'dispatched', 'delivered', 'cancelled'])->default('pending');
            $table->text('order_notes')->nullable();
            $table->integer('number_of_boxes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // If you want to associate with users table
            $table->json('cart_data')->nullable();
            $table->text('address_details')->nullable();
            $table->string('house_block')->nullable();
            $table->string('area_road')->nullable();
            $table->string('save_as')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->timestamp('delivery_date')->nullable(); // Already exists – okay to keep
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('delivery_time')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            //  Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
