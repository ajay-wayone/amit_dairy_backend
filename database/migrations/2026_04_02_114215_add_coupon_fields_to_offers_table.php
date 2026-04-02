<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->unique()->after('offer');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('coupon_code');
            $table->boolean('status')->default(true)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_percentage', 'status']);
        });
    }
};
