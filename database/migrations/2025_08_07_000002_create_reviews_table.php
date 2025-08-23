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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->comment('Rating from 1 to 5');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure one review per user per product
            $table->unique(['user_id', 'product_id']);
            
            // Index for better performance
            $table->index(['product_id', 'is_approved', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
}; 