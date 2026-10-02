<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            // JSON for plain settings, Laravel-encrypted string for secrets.
            $table->longText('value')->nullable();
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_settings');
    }
};
