<?php

namespace App\Services\News\Images;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Image;

/** Validates raw image bytes, crops to the configured size, converts to WebP and stores them on the public disk. */
class ImageProcessor
{
    private const ALLOWED_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF];

    private const MAX_PIXELS = 40_000_000;

    /** @return string path relative to the public disk */
    public function store(string $binary, string $slug): string
    {
        $maxBytes = (int) config('news.images.max_download_bytes');
        if ($binary === '' || strlen($binary) > $maxBytes) {
            throw new RuntimeException('Ảnh rỗng hoặc vượt quá giới hạn dung lượng.');
        }

        $info = @getimagesizefromstring($binary);
        if ($info === false || ! in_array($info[2], self::ALLOWED_TYPES, true) || $info[0] * $info[1] > self::MAX_PIXELS) {
            throw new RuntimeException('Dữ liệu không phải ảnh hợp lệ.');
        }

        $source = tempnam(sys_get_temp_dir(), 'news-src-');
        $target = $source.'.webp';

        try {
            file_put_contents($source, $binary);
            Image::useImageDriver(ImageDriver::Gd)
                ->loadFile($source)
                ->fit(Fit::Crop, (int) config('news.images.width'), (int) config('news.images.height'))
                ->format('webp')
                ->quality((int) config('news.images.quality'))
                ->save($target);

            $path = sprintf('news/%s/%s-%s.webp', now()->format('Y/m'), Str::limit(Str::slug($slug) ?: 'tin', 60, ''), Str::lower(Str::random(6)));
            Storage::disk('public')->put($path, (string) file_get_contents($target));

            return $path;
        } finally {
            @unlink($source);
            @unlink($target);
        }
    }
}
