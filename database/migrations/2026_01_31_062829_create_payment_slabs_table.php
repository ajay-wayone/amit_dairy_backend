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
        Schema::create('payment_slabs', function (Blueprint $table) {
            $table->id();
            $table->float('min_km');
            $table->float('max_km')->nullable();
            $table->integer('advance_percentage');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_slabs');
    }

};
