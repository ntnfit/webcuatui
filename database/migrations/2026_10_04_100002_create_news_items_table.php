<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('news_sources')->cascadeOnDelete();
            $table->string('guid_hash', 64)->unique();
            $table->string('url', 1000);
            $table->string('title', 500);
            $table->timestamp('published_at')->nullable()->index();
            $table->text('excerpt')->nullable();
            // Plain text of the feed's own content:encoded when present (never scraped from the page).
            $table->text('content')->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->integer('score')->default(0);
            $table->enum('status', ['new', 'selected', 'published', 'rejected', 'failed'])->default('new')->index();
            $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_items');
    }
};
