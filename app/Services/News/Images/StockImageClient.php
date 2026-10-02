<?php

namespace App\Services\News\Images;

use App\Services\News\NewsSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Searches Unsplash (preferred) and Pexels with the API key saved in the settings.
 * Both APIs are used the way their terms require: Unsplash downloads go through the returned
 * URL and ping the download endpoint, and both photographers are credited by the caller.
 */
class StockImageClient
{
    private const UTM = 'utm_source=toilamerp&utm_medium=referral';

    public function __construct(private readonly NewsSettings $settings) {}

    public function isConfigured(): bool
    {
        return $this->settings->secret('unsplash') !== null || $this->settings->secret('pexels') !== null;
    }

    /**
     * @return list<StockPhoto> empty when no key is configured or nothing matched
     */
    public function search(string $query, int $count): array
    {
        foreach (['unsplash', 'pexels'] as $provider) {
            $key = $this->settings->secret($provider);
            if ($key === null) {
                continue;
            }
            try {
                $photos = $provider === 'unsplash' ? $this->unsplash($key, $query, $count) : $this->pexels($key, $query, $count);
                if ($photos !== []) {
                    return $photos;
                }
            } catch (Throwable $e) {
                // A stock outage must never block a post: the caller falls back to the generated cover.
                Log::warning('Stock image search failed', ['provider' => $provider, 'error' => $e->getMessage()]);
            }
        }

        return [];
    }

    /** @throws RuntimeException when the image cannot be downloaded */
    public function download(StockPhoto $photo): string
    {
        if ($photo->provider === 'unsplash' && $photo->trackUrl && ($key = $this->settings->secret('unsplash'))) {
            // Required by the Unsplash API guidelines whenever a photo is used. Failure is not fatal.
            try {
                $this->http()->withHeaders(['Authorization' => 'Client-ID '.$key])->get($photo->trackUrl);
            } catch (Throwable) {
            }
        }

        $response = $this->http()->get($photo->downloadUrl);
        if (! $response->successful()) {
            throw new RuntimeException('Tải ảnh stock thất bại: HTTP '.$response->status());
        }

        return $response->body();
    }

    /** @return list<StockPhoto> */
    private function unsplash(string $key, string $query, int $count): array
    {
        $response = $this->http()
            ->withHeaders(['Authorization' => 'Client-ID '.$key, 'Accept-Version' => 'v1'])
            ->get('https://api.unsplash.com/search/photos', ['query' => $query, 'per_page' => max(3, $count), 'orientation' => 'landscape', 'content_filter' => 'high'])
            ->throw();

        $photos = [];
        foreach ($response->json('results', []) as $hit) {
            $url = $hit['urls']['regular'] ?? null;
            if (! $url || count($photos) >= $count) {
                continue;
            }
            $photos[] = new StockPhoto(
                provider: 'unsplash',
                downloadUrl: $url,
                photographer: $hit['user']['name'] ?? 'Unsplash',
                photographerUrl: ($hit['user']['links']['html'] ?? 'https://unsplash.com').'?'.self::UTM,
                photoUrl: ($hit['links']['html'] ?? 'https://unsplash.com').'?'.self::UTM,
                alt: $hit['alt_description'] ?? null,
                trackUrl: $hit['links']['download_location'] ?? null,
            );
        }

        return $photos;
    }

    /** @return list<StockPhoto> */
    private function pexels(string $key, string $query, int $count): array
    {
        $response = $this->http()
            ->withHeaders(['Authorization' => $key])
            ->get('https://api.pexels.com/v1/search', ['query' => $query, 'per_page' => max(3, $count), 'orientation' => 'landscape'])
            ->throw();

        $photos = [];
        foreach ($response->json('photos', []) as $hit) {
            $url = $hit['src']['large2x'] ?? $hit['src']['large'] ?? null;
            if (! $url || count($photos) >= $count) {
                continue;
            }
            $photos[] = new StockPhoto(
                provider: 'pexels',
                downloadUrl: $url,
                photographer: $hit['photographer'] ?? 'Pexels',
                photographerUrl: $hit['photographer_url'] ?? 'https://www.pexels.com',
                photoUrl: $hit['url'] ?? 'https://www.pexels.com',
                alt: $hit['alt'] ?? null,
            );
        }

        return $photos;
    }

    private function http(): PendingRequest
    {
        return Http::withHeaders(['User-Agent' => config('news.http.user_agent')])->timeout((int) config('news.http.timeout'));
    }
}
