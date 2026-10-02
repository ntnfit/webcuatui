<?php

namespace App\Services\News\Images;

/** Cover plus the inline images chosen for one article. */
final class ImageSet
{
    /** @param  list<StoredImage>  $inline */
    public function __construct(
        public readonly StoredImage $cover,
        public readonly array $inline = [],
    ) {}

    /** @return list<StoredImage> every image that carries a credit */
    public function credited(): array
    {
        return array_values(array_filter([$this->cover, ...$this->inline], fn (StoredImage $i) => $i->creditHtml !== null));
    }
}
