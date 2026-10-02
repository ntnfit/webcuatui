<?php

namespace App\Services\News\Content;

use App\Models\NewsItem;
use App\Services\News\Images\ImageSet;
use App\Services\News\Images\StoredImage;

/**
 * Assembles the final post HTML: the sanitised article, inline figures, and the
 * "Nguồn tham khảo" block. The source block is built here from the stored item, never by the
 * model, so the original link, source name and image credits are always correct.
 */
class ArticleBodyBuilder
{
    public function build(string $sanitizedBody, NewsItem $item, ImageSet $images): string
    {
        $parts = preg_split('/(?=<h2>)/', $sanitizedBody, -1, PREG_SPLIT_NO_EMPTY) ?: [$sanitizedBody];

        // Inline figures go in front of the 2nd and 3rd section; leftovers close the article.
        $leftover = [];
        foreach ($images->inline as $n => $image) {
            $at = $n + 1;
            if (isset($parts[$at]) && $at < count($parts) - 1) {
                $parts[$at] = $this->figure($image).$parts[$at];
            } else {
                $leftover[] = $this->figure($image);
            }
        }

        return trim(implode('', $parts).implode('', $leftover)."\n".$this->sourceBlock($item, $images));
    }

    public function sourceBlock(NewsItem $item, ImageSet $images): string
    {
        $link = '<a href="'.e($item->url).'" target="_blank" rel="nofollow noopener">'.e($item->title).'</a>';
        $name = e((string) ($item->source?->name ?? parse_url($item->url, PHP_URL_HOST)));

        $html = "<h2>Nguồn tham khảo</h2>\n<ul>\n<li>Bài gốc: {$link} — {$name}</li>\n";
        foreach ($images->credited() as $image) {
            $html .= "<li>{$image->creditHtml}</li>\n";
        }
        $html .= "</ul>\n<p><em>Bài viết được tổng hợp và biên tập lại bằng tiếng Việt từ nguồn trên.</em></p>";

        return $html;
    }

    private function figure(StoredImage $image): string
    {
        $caption = $image->creditHtml !== null ? "<figcaption>{$image->creditHtml}</figcaption>" : '';

        return '<figure><img src="'.e($image->url()).'" alt="'.e($image->alt).'" loading="lazy">'.$caption."</figure>\n";
    }
}
