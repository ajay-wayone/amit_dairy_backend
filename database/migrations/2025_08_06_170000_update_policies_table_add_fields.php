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
        Schema::table('policies', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->boolean('is_active')->default(true)->after('content');
            $table->string('meta_title')->nullable()->after('is_active');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->unsignedBigInteger('last_updated_by')->nullable()->after('meta_description');
            
            // Add foreign key constraint
            $table->foreign('last_updated_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn(['title', 'is_active', 'meta_title', 'meta_description', 'last_updated_by']);
        });
    }
}; 