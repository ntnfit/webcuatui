<?php

namespace Database\Factories;

use App\Models\NewsRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NewsRun> */
class NewsRunFactory extends Factory
{
    protected $model = NewsRun::class;

    public function definition(): array
    {
        return [
            'status' => NewsRun::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(5),
            'finished_at' => now(),
            'fetched' => 0,
            'selected' => 0,
            'published' => 0,
            'rejected' => 0,
            'failed' => 0,
        ];
    }
}
