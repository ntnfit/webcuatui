<?php

namespace App\Services\News\Images;

/** A search hit from a licence-friendly stock API (Unsplash or Pexels). */
final class StockPhoto
{
    public function __construct(
        public readonly string $provider,
        public readonly string $downloadUrl,
        public readonly string $photographer,
        public readonly string $photographerUrl,
        public readonly string $photoUrl,
        public readonly ?string $alt = null,
        /** Unsplash only: endpoint that must be pinged when the photo is used. */
        public readonly ?string $trackUrl = null,
    ) {}

    /** Credit line as safe HTML, e.g. "Photo by NAME on Unsplash". Both links are plain external links. */
    public function creditHtml(): string
    {
        $provider = $this->provider === 'unsplash' ? 'Unsplash' : 'Pexels';
        $link = fn (string $url, string $label) => '<a href="'.e($url).'" target="_blank" rel="nofollow noopener">'.e($label).'</a>';

        return 'Photo by '.$link($this->photographerUrl, $this->photographer).' on '.$link($this->photoUrl, $provider);
    }
}
