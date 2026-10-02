<?php

namespace App\Services\News;

use Anthropic\Core\Exceptions\AnthropicException;
use App\Services\News\Writing\ClaudeClientFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * "Kiểm tra kết nối" for each key: one tiny real call (free token count for Claude, a one-result
 * search for the stock APIs). Reports OK or the error without ever echoing the key.
 */
class ApiKeyTester
{
    public function __construct(
        private readonly NewsSettings $settings,
        private readonly ClaudeClientFactory $clients,
    ) {}

    /** @return array{ok: bool, message: string} */
    public function test(string $provider): array
    {
        $key = $this->settings->secret($provider);
        if ($key === null) {
            return ['ok' => false, 'message' => 'Chưa cấu hình key.'];
        }

        try {
            return match ($provider) {
                'anthropic' => $this->claude($key),
                'unsplash' => $this->stock(Http::withHeaders(['Authorization' => 'Client-ID '.$key])->get('https://api.unsplash.com/search/photos', ['query' => 'technology', 'per_page' => 1])),
                'pexels' => $this->stock(Http::withHeaders(['Authorization' => $key])->get('https://api.pexels.com/v1/search', ['query' => 'technology', 'per_page' => 1])),
            };
        } catch (Throwable $e) {
            return ['ok' => false, 'message' => 'Lỗi: '.mb_substr(str_replace($key, '***', $e->getMessage()), 0, 200)];
        }
    }

    /** @return array{ok: bool, message: string} */
    private function claude(string $key): array
    {
        try {
            $this->clients->make($key)->messages->countTokens(
                model: (string) $this->settings->get('model'),
                messages: [['role' => 'user', 'content' => 'ping']],
            );
        } catch (AnthropicException $e) {
            return ['ok' => false, 'message' => 'Claude từ chối: '.mb_substr(str_replace($key, '***', $e->getMessage()), 0, 200)];
        }

        return ['ok' => true, 'message' => 'Kết nối Claude thành công (model '.$this->settings->get('model').').'];
    }

    /** @return array{ok: bool, message: string} */
    private function stock(Response $response): array
    {
        return $response->successful()
            ? ['ok' => true, 'message' => 'Kết nối thành công.']
            : ['ok' => false, 'message' => 'API trả về HTTP '.$response->status().($response->status() === 401 ? ' (key không hợp lệ).' : '.')];
    }
}
