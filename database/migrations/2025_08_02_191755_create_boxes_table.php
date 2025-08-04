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
       // database/migrations/xxxx_xx_xx_create_boxes_table.php

Schema::create('boxes', function (Blueprint $table) {
    $table->id();
    $table->string('box_name');
    $table->string('box_image');
    $table->decimal('box_price', 10, 2);
    $table->boolean('is_active')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
