<?php

namespace Database\Factories;

use App\Models\NewsItem;
use App\Models\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NewsItem> */
class NewsItemFactory extends Factory
{
    protected $model = NewsItem::class;

    public function definition(): array
    {
        $url = 'https://news.example.test/'.fake()->unique()->slug();

        return [
            'source_id' => NewsSource::factory(),
            'guid_hash' => NewsItem::hashFor($url),
            'url' => $url,
            'title' => fake()->unique()->sentence(8),
            'published_at' => now()->subHours(2),
            'excerpt' => fake()->paragraph(),
            'score' => 50,
            'status' => NewsItem::STATUS_NEW,
        ];
    }

    public function status(string $status): static
    {
        return $this->state(['status' => $status]);
    }
}
