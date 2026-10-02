<?php

namespace App\Services\News\Writing;

use Anthropic\Client;
use Anthropic\Core\Exceptions\AnthropicException;
use App\Models\NewsItem;
use App\Services\News\NewsSettings;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * ArticleWriter backed by the official Anthropic SDK with schema-constrained JSON output.
 * The API key is read from the settings (DB, then env) only when a request is made and
 * is never logged.
 */
class ClaudeArticleWriter implements ArticleWriter
{
    /** @param  (Closure(string): object)|null  $clientFactory  builds the SDK client from the API key (swapped in tests) */
    public function __construct(
        private readonly NewsSettings $settings,
        private readonly ArticlePrompt $prompt,
        private readonly ?Closure $clientFactory = null,
    ) {}

    public function isConfigured(): bool
    {
        return $this->settings->secret('anthropic') !== null;
    }

    public function write(NewsItem $item): ArticleDraft
    {
        $key = $this->settings->secret('anthropic');
        if ($key === null) {
            throw new ArticleWriterException('Chưa cấu hình ANTHROPIC_API_KEY.');
        }

        $model = (string) $this->settings->get('model');
        $outputConfig = ['format' => ['type' => 'json_schema', 'schema' => $this->prompt->schema()]];
        if (ClaudeModels::supportsEffort($model)) {
            $outputConfig['effort'] = (string) $this->settings->get('effort');
        }

        try {
            $client = $this->clientFactory ? ($this->clientFactory)($key) : new Client(apiKey: $key, requestOptions: ['timeout' => 300]);
            $message = $client->messages->create(
                model: $model,
                maxTokens: (int) $this->settings->get('max_tokens'),
                system: [['type' => 'text', 'text' => $this->prompt->system(), 'cacheControl' => ['type' => 'ephemeral']]],
                messages: [['role' => 'user', 'content' => $this->prompt->user($item)]],
                outputConfig: $outputConfig,
            );
        } catch (AnthropicException $e) {
            // The SDK message carries the HTTP status and API error text, never the key.
            Log::warning('Claude request failed', ['item' => $item->id, 'error' => $e->getMessage()]);
            throw new ArticleWriterException('Lỗi Claude API: '.mb_substr($e->getMessage(), 0, 300), 0, $e);
        }

        if ($message->stopReason === 'refusal') {
            $category = $message->stopDetails?->category;

            return ArticleDraft::rejected('Claude từ chối xử lý (refusal'.($category ? ": {$category}" : '').').');
        }
        if (! in_array($message->stopReason, ['end_turn', 'stop_sequence'], true)) {
            throw new ArticleWriterException('Phản hồi chưa hoàn chỉnh (stop_reason='.($message->stopReason ?? 'null').').');
        }

        $text = null;
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text = $block->text;
                break;
            }
        }

        $data = is_string($text) ? json_decode($text, true) : null;
        if (! is_array($data)) {
            throw new ArticleWriterException('Phản hồi không phải JSON hợp lệ.');
        }

        return ArticleDraft::fromArray($data);
    }
}
