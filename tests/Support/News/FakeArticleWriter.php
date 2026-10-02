<?php

namespace Tests\Support\News;

use App\Models\NewsItem;
use App\Services\News\Writing\ArticleDraft;
use App\Services\News\Writing\ArticleWriter;
use App\Services\News\Writing\ArticleWriterException;
use Closure;

/** Test double for the Claude writer: never touches the network. */
class FakeArticleWriter implements ArticleWriter
{
    /** @var list<string> titles of the items it was asked to write */
    public array $calls = [];

    public bool $configured = true;

    /** @param  (Closure(NewsItem): ArticleDraft)|null  $responder */
    public function __construct(private ?Closure $responder = null) {}

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function write(NewsItem $item): ArticleDraft
    {
        $this->calls[] = $item->title;

        if ($this->responder) {
            return ($this->responder)($item);
        }

        return NewsFixtures::draft(['title' => 'Bài tiếng Việt về '.$item->title, 'slug' => 'bai-'.str($item->title)->slug()]);
    }

    public static function failing(string $message = 'API down'): self
    {
        return new self(fn () => throw new ArticleWriterException($message));
    }
}
