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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // $table->string('product_code')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('product_image')->nullable();
            $table->json('types')->nullable(); // For product types like sizes/flavors etc.
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_type')->nullable(); // e.g., kg, g, L, ml, etc.
            $table->integer('min_order')->default(1);
            $table->integer('max_order')->nullable();
            $table->boolean('best_seller')->default(false);
            $table->boolean('specialities')->default(false); // Can change to text if storing multiple specialities
            $table->boolean('status')->default(true);
            $table->string('featured_type')->nullable(); 
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
