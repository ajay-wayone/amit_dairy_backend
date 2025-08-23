<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->string('email')->index();
            $table->string('otp');           // OTP code
            $table->timestamp('expires_at'); // OTP expiry time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
