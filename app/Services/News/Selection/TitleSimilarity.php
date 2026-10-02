<?php

namespace App\Services\News\Selection;

use Illuminate\Support\Str;

/** Accent-insensitive title comparison used to spot the same story across feeds. */
class TitleSimilarity
{
    public static function normalize(string $title): string
    {
        $ascii = Str::lower(Str::ascii($title));

        return trim(preg_replace('/[^a-z0-9]+/', ' ', $ascii) ?? $ascii);
    }

    /** Similarity in 0..1 between two already normalised titles. */
    public static function ratio(string $a, string $b): float
    {
        if ($a === '' || $b === '') {
            return 0.0;
        }
        if ($a === $b) {
            return 1.0;
        }

        $la = strlen($a);
        $lb = strlen($b);
        // Very different lengths can never reach a high ratio; skip the expensive comparison.
        if (min($la, $lb) / max($la, $lb) < 0.5) {
            return 0.0;
        }

        similar_text(substr($a, 0, 160), substr($b, 0, 160), $percent);

        return $percent / 100;
    }

    /** @param iterable<string> $normalizedPool */
    public static function matchesAny(string $normalizedTitle, iterable $normalizedPool, float $threshold): bool
    {
        foreach ($normalizedPool as $other) {
            if (self::ratio($normalizedTitle, $other) >= $threshold) {
                return true;
            }
        }

        return false;
    }
}
