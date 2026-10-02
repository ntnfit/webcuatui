<?php

namespace App\Services\News\Images;

/** An image already saved on the public disk, with the attribution that must be shown next to it. */
final class StoredImage
{
    /**
     * @param  string  $path  path relative to the public disk (what posts.cover_photo_path expects)
     * @param  string|null  $creditHtml  safe, ready to embed HTML credit line, null for self generated art
     */
    public function __construct(
        public readonly string $path,
        public readonly string $alt,
        public readonly ?string $creditHtml = null,
    ) {}

    public function url(): string
    {
        return '/storage/'.ltrim($this->path, '/');
    }
}
