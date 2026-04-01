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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('box_id')->nullable()->after('product_id');
            $table->string('box_name')->nullable()->after('box_id');
            $table->decimal('box_price', 10, 2)->nullable()->after('box_name');
            $table->integer('box_qty')->nullable()->after('box_price');
            $table->text('custom_text')->nullable()->after('box_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['box_id', 'box_name', 'box_price', 'box_qty', 'custom_text']);
        });
    }
};
