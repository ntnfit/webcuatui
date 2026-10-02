<?php

namespace App\Services\News\Images;

use GdImage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Draws the branded fallback cover: a dark terminal card with the post title, the site name
 * and the category tag. Uses the bundled Be Vietnam Pro font (SIL OFL, resources/fonts) so
 * every Vietnamese diacritic renders.
 */
class CoverGenerator
{
    private const FONT_BOLD = 'BeVietnamPro-Bold.ttf';

    private const FONT_REGULAR = 'BeVietnamPro-Regular.ttf';

    /** @return string PNG bytes */
    public function render(string $title, string $category, string $siteName): string
    {
        $w = (int) config('news.images.width');
        $h = (int) config('news.images.height');
        $im = imagecreatetruecolor($w, $h);
        if ($im === false) {
            throw new RuntimeException('Không tạo được canvas ảnh bìa.');
        }

        $c = fn (string $hex) => imagecolorallocate($im, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
        imagefilledrectangle($im, 0, 0, $w, $h, $c('0d1117'));
        imagefilledrectangle($im, 0, 0, $w, 64, $c('161b22'));
        foreach (['ff5f56' => 42, 'ffbd2e' => 74, '27c93f' => 106] as $hex => $x) {
            imagefilledellipse($im, $x, 32, 18, 18, $c($hex));
        }
        imagefilledrectangle($im, 0, 64, $w, 65, $c('30363d'));

        $bold = resource_path('fonts/'.self::FONT_BOLD);
        $regular = resource_path('fonts/'.self::FONT_REGULAR);

        if (function_exists('imagettftext') && is_file($bold) && is_file($regular)) {
            $this->drawWithFonts($im, $c, $title, $category, $siteName, $bold, $regular, $w, $h);
        } else {
            // No FreeType: ASCII-only text with the built-in font so the post still gets a cover.
            imagestring($im, 5, 60, 120, Str::ascii($title), $c('ffffff'));
        }

        ob_start();
        imagepng($im);
        $png = (string) ob_get_clean();
        imagedestroy($im);

        return $png;
    }

    private function drawWithFonts(GdImage $im, callable $c, string $title, string $category, string $siteName, string $bold, string $regular, int $w, int $h): void
    {
        imagettftext($im, 17, 0, 140, 40, $c('8b949e'), $regular, '~/'.$siteName.' — tin công nghệ');
        imagettftext($im, 24, 0, 60, 138, $c('3fb950'), $regular, '$ cat tin-moi.md');

        $maxWidth = $w - 120;
        [$size, $lines] = $this->fitTitle($title, $bold, $maxWidth, 4);
        $y = 218;
        $lineHeight = (int) round($size * 1.7);
        foreach ($lines as $line) {
            imagettftext($im, $size, 0, 60, $y, $c('f0f6fc'), $bold, $line);
            $baseline = $y;
            $y += $lineHeight;
        }
        // Blinking-cursor block after the last line.
        $last = imagettfbbox($size, 0, $bold, end($lines) ?: ' ');
        $cursorX = 60 + $last[2] + 14;
        imagefilledrectangle($im, $cursorX, $baseline - (int) round($size * 1.25), $cursorX + (int) round($size * 0.6), $baseline + 6, $c('3fb950'));

        // Category chip and site name along the bottom edge.
        $tag = '#'.mb_strtolower($category);
        $box = imagettfbbox(20, 0, $regular, $tag);
        $chipW = $box[2] - $box[0] + 40;
        imagefilledrectangle($im, 60, $h - 100, 60 + $chipW, $h - 52, $c('0c2d6b'));
        imagettftext($im, 20, 0, 80, $h - 66, $c('58a6ff'), $regular, $tag);

        $siteBox = imagettfbbox(22, 0, $bold, $siteName);
        imagettftext($im, 22, 0, $w - 60 - ($siteBox[2] - $siteBox[0]), $h - 62, $c('8b949e'), $bold, $siteName);
    }

    /**
     * Largest font size at which the title fits in $maxLines lines.
     *
     * @return array{0: int, 1: list<string>}
     */
    private function fitTitle(string $title, string $font, int $maxWidth, int $maxLines): array
    {
        foreach ([46, 40, 36, 32, 28] as $size) {
            $lines = $this->wrap($title, $font, $size, $maxWidth);
            if (count($lines) <= $maxLines) {
                return [$size, $lines];
            }
        }

        $lines = array_slice($this->wrap($title, $font, 28, $maxWidth), 0, $maxLines);
        $lines[$maxLines - 1] = rtrim($lines[$maxLines - 1], ' ,.;:').'…';

        return [28, $lines];
    }

    /** @return list<string> */
    private function wrap(string $text, string $font, int $size, int $maxWidth): array
    {
        $lines = [];
        $line = '';
        foreach (preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            $try = $line === '' ? $word : $line.' '.$word;
            $box = imagettfbbox($size, 0, $font, $try);
            if ($maxWidth < $box[2] - $box[0] && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $try;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }

        return $lines;
    }
}
