<?php

use Anthropic\Core\Exceptions\AnthropicException;
use App\Models\NewsItem;
use App\Models\NewsSetting;
use App\Models\NewsSource;
use App\Services\News\NewsSettings;
use App\Services\News\Writing\ArticleDraft;
use App\Services\News\Writing\ArticlePrompt;
use App\Services\News\Writing\ArticleWriter;
use App\Services\News\Writing\ArticleWriterException;
use App\Services\News\Writing\ClaudeArticleWriter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Tests\Support\News\FakeClaudeClients;
use Tests\Support\News\NewsFixtures;

function newsWriterItem(): NewsItem
{
    return NewsItem::factory()->for(NewsSource::factory(), 'source')->create();
}

it('lets database values override config and config override the raw default', function () {
    config(['news.posts_per_day' => 10]);
    $settings = app(NewsSettings::class);

    expect($settings->get('posts_per_day'))->toBe(10);

    $settings->set('posts_per_day', 4);
    $settings->set('boost_keywords', ['sap', 'erp']);

    expect($settings->get('posts_per_day'))->toBe(4)
        ->and($settings->get('boost_keywords'))->toBe(['sap', 'erp']);

    $settings->reset('posts_per_day');
    expect($settings->get('posts_per_day'))->toBe(10);
});

it('refuses unknown setting keys', function () {
    expect(fn () => app(NewsSettings::class)->set('made.up', 1))->toThrow(InvalidArgumentException::class)
        ->and(fn () => app(NewsSettings::class)->secret('openai'))->toThrow(InvalidArgumentException::class);
});

it('stores api keys encrypted, prefers them over env and falls back to env when cleared', function () {
    $settings = app(NewsSettings::class);
    config(['news.keys.anthropic' => 'env-key-123']);

    expect($settings->secret('anthropic'))->toBe('env-key-123')->and($settings->secretSource('anthropic'))->toBe('env');

    $settings->setSecret('anthropic', 'sk-ant-db-secret-999');

    $raw = NewsSetting::where('key', 'secret.anthropic')->firstOrFail()->getAttributes()['value'];
    expect($raw)->not->toContain('sk-ant-db-secret-999')
        ->and(Crypt::decryptString($raw))->toBe('sk-ant-db-secret-999')
        ->and($settings->secret('anthropic'))->toBe('sk-ant-db-secret-999')
        ->and($settings->secretSource('anthropic'))->toBe('db')
        ->and(NewsSetting::first()->toJson())->not->toContain('sk-ant');

    $settings->clearSecret('anthropic');
    expect($settings->secret('anthropic'))->toBe('env-key-123')->and($settings->secretSource('anthropic'))->toBe('env');

    config(['news.keys.anthropic' => null]);
    expect($settings->secret('anthropic'))->toBeNull()->and($settings->secretSource('anthropic'))->toBeNull();
});

it('calls Claude with the configured model, effort, schema and fenced untrusted data', function () {
    app(NewsSettings::class)->setSecret('anthropic', 'sk-test-key');
    [$clients, $rec] = FakeClaudeClients::make();
    $writer = new ClaudeArticleWriter(app(NewsSettings::class), app(ArticlePrompt::class), $clients);

    $draft = $writer->write(newsWriterItem());

    expect($draft)->toBeInstanceOf(ArticleDraft::class)->and($draft->title)->toBe('Tiêu đề')
        ->and($rec->args['model'])->toBe('claude-opus-5-5')
        ->and($rec->args['maxTokens'])->toBe(6000)
        ->and($rec->args['outputConfig']['effort'])->toBe('low')
        ->and($rec->args['outputConfig']['format']['type'])->toBe('json_schema')
        ->and($rec->args['outputConfig']['format']['schema']['additionalProperties'])->toBeFalse()
        ->and($rec->args['system'][0]['text'])->toContain('QUY TẮC BẮT BUỘC')
        ->and($rec->args['messages'][0]['content'])->toContain('<du_lieu_tin_khong_tin_cay')
        ->and($rec->args)->not->toHaveKey('thinking');
});

it('does not send effort to models that do not support it', function () {
    app(NewsSettings::class)->setSecret('anthropic', 'k');
    app(NewsSettings::class)->set('model', 'claude-haiku-4-5');
    [$clients, $rec] = FakeClaudeClients::make();

    (new ClaudeArticleWriter(app(NewsSettings::class), app(ArticlePrompt::class), $clients))->write(newsWriterItem());

    expect($rec->args['outputConfig'])->not->toHaveKey('effort')->and($rec->args['model'])->toBe('claude-haiku-4-5');
});

it('turns a refusal into a rejected draft and bad answers into writer errors', function () {
    $item = newsWriterItem();
    $settings = app(NewsSettings::class);
    $settings->setSecret('anthropic', 'k');
    $make = fn (Closure $respond) => new ClaudeArticleWriter($settings, app(ArticlePrompt::class), FakeClaudeClients::make($respond)[0]);

    $refusal = $make(fn () => (object) ['stopReason' => 'refusal', 'stopDetails' => (object) ['category' => 'cyber'], 'content' => []])->write($item);
    expect($refusal->publishable)->toBeFalse()->and($refusal->rejectReason)->toContain('refusal')->toContain('cyber');

    expect(fn () => $make(fn () => FakeClaudeClients::reply(NewsFixtures::payload(), 'max_tokens'))->write($item))->toThrow(ArticleWriterException::class, 'max_tokens')
        ->and(fn () => $make(fn () => (object) ['stopReason' => 'end_turn', 'stopDetails' => null, 'content' => [(object) ['type' => 'text', 'text' => 'not json']]])->write($item))->toThrow(ArticleWriterException::class)
        ->and(fn () => $make(fn () => FakeClaudeClients::reply(['publishable' => true]))->write($item))->toThrow(ArticleWriterException::class);
});

it('wraps API errors per item and never logs the key', function () {
    $item = newsWriterItem();
    $settings = app(NewsSettings::class);
    $settings->setSecret('anthropic', 'sk-ant-super-secret');
    $writer = new ClaudeArticleWriter($settings, app(ArticlePrompt::class), FakeClaudeClients::make(fn () => throw new AnthropicException('429 rate limited'))[0]);

    Log::shouldReceive('warning')->once()->withArgs(fn ($message, $context = []) => ! str_contains($message.json_encode($context), 'sk-ant-super-secret'));

    expect(fn () => $writer->write($item))->toThrow(ArticleWriterException::class, '429');
});

it('reports a clear error instead of calling the API when no key exists', function () {
    config(['news.keys.anthropic' => null]);
    $writer = app(ArticleWriter::class);

    expect($writer->isConfigured())->toBeFalse()
        ->and(fn () => $writer->write(newsWriterItem()))->toThrow(ArticleWriterException::class, 'ANTHROPIC_API_KEY');
});
