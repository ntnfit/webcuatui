<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = resolve(User::class)->getTable();
        $columnName ='profile_photo_path';

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 155)->unique();
            $table->string('slug', 155)->unique();
            $table->string('name_en', 155)->unique();
            $table->string('slug_en', 155)->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug');
            $table->string('slug_en')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('sub_title_en')->nullable();
            $table->longText('body');
            $table->longText('body_en')->nullable();
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

        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->foreignId("category_id")
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('seo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->string('title');
            $table->json('keywords')->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('news_letters', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->boolean('subscribed')->default(true);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('name_en', 50)->unique();
            $table->string('slug', 155)->unique();
            $table->string('slug_en', 155)->unique();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->foreignId("tag_id")
                ->constrained('tags')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // Check if the column exists
        if (!Schema::hasColumn($tableName, $columnName)) {
            // Column doesn't exist, so add it to the table
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->string($columnName)->nullable();
            });
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('seo_details');
        Schema::dropIfExists('news_letters');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');
    }
};
