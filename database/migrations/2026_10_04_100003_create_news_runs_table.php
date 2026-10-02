<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_runs', function (Blueprint $table) {
            $table->id();
            // running while in progress, completed when it finished, aborted when it could not start (e.g. missing key).
            $table->enum('status', ['running', 'completed', 'aborted'])->default('running')->index();
            $table->timestamp('started_at')->index();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('fetched')->default(0);
            $table->unsignedInteger('selected')->default(0);
            $table->unsignedInteger('published')->default(0);
            $table->unsignedInteger('rejected')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_runs');
    }
};
