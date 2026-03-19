<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->string('order_id')->nullable()->change();
            $table->text('delivery_address')->nullable()->change();
            $table->string('delivery_city')->nullable()->change();
            $table->string('delivery_state')->nullable()->change();
            $table->string('delivery_pincode')->nullable()->change();
            $table->string('house_block')->nullable()->change();
            $table->string('area_road')->nullable()->change();
            $table->string('save_as')->nullable()->change();
            $table->text('order_notes')->nullable()->change();
            $table->integer('number_of_boxes')->nullable()->change();
            $table->string('receiver_name')->nullable()->change();
            $table->string('receiver_phone')->nullable()->change();
            $table->string('delivery_time')->nullable()->change();
            $table->string('latitude')->nullable()->change();
            $table->string('longitude')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->string('order_id')->nullable(false)->change();
            $table->text('delivery_address')->nullable(false)->change();
            $table->string('delivery_city')->nullable(false)->change();
            $table->string('delivery_state')->nullable(false)->change();
            $table->string('delivery_pincode')->nullable(false)->change();
            $table->string('house_block')->nullable(false)->change();
            $table->string('area_road')->nullable(false)->change();
            $table->string('save_as')->nullable(false)->change();
            $table->text('order_notes')->nullable(false)->change();
            $table->integer('number_of_boxes')->nullable(false)->change();
            $table->string('receiver_name')->nullable(false)->change();
            $table->string('receiver_phone')->nullable(false)->change();
            $table->string('delivery_time')->nullable(false)->change();
            $table->string('latitude')->nullable(false)->change();
            $table->string('longitude')->nullable(false)->change();
        });
    }
};