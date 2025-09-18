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
        Schema::create('subscription_admin_products', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->integer('duration_days');
            $table->decimal('price  ', 10, 2);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

        });
    }

};
