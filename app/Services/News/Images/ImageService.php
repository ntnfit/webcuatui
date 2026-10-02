<?php

namespace App\Services\News\Images;

use App\Models\NewsItem;
use App\Services\News\NewsSettings;
use App\Services\News\Writing\ArticleDraft;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Chooses the images of one article. Every article gets a cover: a stock photo when allowed
 * and available, otherwise the generated branded card. Stock failures never block publishing.
 */
class ImageService
{
    public function __construct(
        private readonly NewsSettings $settings,
        private readonly StockImageClient $stock,
        private readonly ImageProcessor $processor,
        private readonly CoverGenerator $generator,
    ) {}

    public function build(ArticleDraft $draft, NewsItem $item, string $category): ImageSet
    {
        $mode = (string) $this->settings->get('images.mode');
        $useStock = $mode !== 'generated_only' && $this->stock->isConfigured();
        $inlineWanted = $useStock ? max(0, min(2, (int) $this->settings->get('images.inline'))) : 0;

        $photos = [];
        if ($useStock) {
            $photos = $this->stock->search($draft->imageQuery, $inlineWanted + 1);
        }

        $cover = null;
        if ($mode !== 'generated_only' && config('news.images.use_source_image')) {
            $cover = $this->fromSource($item, $draft);
        }
        if ($cover === null && $mode === 'stock_first') {
            $cover = $this->firstStored($photos, $draft);
        }
        $cover ??= $this->generated($draft, $category);

        $inline = [];
        foreach ($photos as $photo) {
            if (count($inline) >= $inlineWanted) {
                break;
            }
            if ($stored = $this->storePhoto($photo, $draft)) {
                $inline[] = $stored;
            }
        }

        return new ImageSet($cover, $inline);
    }

    /** @param  list<StockPhoto>  $photos consumed from the front when one is stored as cover */
    private function firstStored(array &$photos, ArticleDraft $draft): ?StoredImage
    {
        while ($photos !== []) {
            $photo = array_shift($photos);
            if ($stored = $this->storePhoto($photo, $draft)) {
                return $stored;
            }
        }

        return null;
    }

    private function storePhoto(StockPhoto $photo, ArticleDraft $draft): ?StoredImage
    {
        try {
            $path = $this->processor->store($this->stock->download($photo), $draft->slug);

            return new StoredImage($path, $draft->imageAlt, $photo->creditHtml());
        } catch (Throwable $e) {
            Log::warning('Stock image skipped', ['provider' => $photo->provider, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /** Opt-in only (NEWS_USE_SOURCE_IMAGE): the source image is re-encoded and credited to the source. */
    private function fromSource(NewsItem $item, ArticleDraft $draft): ?StoredImage
    {
        if (! $item->image_url) {
            return null;
        }
        try {
            $response = Http::withHeaders(['User-Agent' => config('news.http.user_agent')])->timeout((int) config('news.http.timeout'))->get($item->image_url);
            if (! $response->successful()) {
                return null;
            }
            $credit = 'Ảnh: '.e((string) $item->source?->name).' (ảnh trong bài gốc)';

            return new StoredImage($this->processor->store($response->body(), $draft->slug), $draft->imageAlt, $credit);
        } catch (Throwable $e) {
            Log::warning('Source image skipped', ['item' => $item->id, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function generated(ArticleDraft $draft, string $category): StoredImage
    {
        $png = $this->generator->render($draft->title, $category, (string) config('news.site_name'));

        return new StoredImage($this->processor->store($png, $draft->slug), $draft->imageAlt);
    }
}
