<?php

namespace App\Services\News\Writing;

use App\Models\NewsItem;

/** Turns one news item into a Vietnamese SEO article draft. */
interface ArticleWriter
{
    /** False when credentials are missing, so a run can stop before touching any item. */
    public function isConfigured(): bool;

    /**
     * @throws ArticleWriterException on API errors or unusable output
     */
    public function write(NewsItem $item): ArticleDraft;
}
