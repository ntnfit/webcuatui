<?php

namespace Database\Factories;

use App\Models\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NewsSource> */
class NewsSourceFactory extends Factory
{
    protected $model = NewsSource::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'type' => 'rss',
            'url' => 'https://feeds.example.test/'.fake()->unique()->slug().'.xml',
            'language' => 'en',
            'weight' => 1,
            'enabled' => true,
        ];
    }
}
