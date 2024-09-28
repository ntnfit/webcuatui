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
        Schema::create('categrtool', function (Blueprint $table) {
            $table->id();
            $table->string('name', 155)->unique();
            $table->string('slug', 155)->unique();
            $table->timestamps();
        });
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('sub_title')->nullable();
            $table->longText('body');
            $table->enum('status', ['published', 'scheduled', 'pending'])->default('pending');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('scheduled_for')->nullable();
            $table->string('cover_photo_path');
            $table->string('photo_alt_text');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('category_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->foreignId("category_id")
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_tools');
        Schema::dropIfExists('tools');
        Schema::dropIfExists('categrtool');
    }
};
