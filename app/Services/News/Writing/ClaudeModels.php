<?php

namespace App\Services\News\Writing;

/**
 * Models selectable from the admin page, with list prices (USD per million tokens)
 * and the rough token footprint of one article, used for the daily cost estimate.
 */
final class ClaudeModels
{
    /** @var array<string, array{label: string, input: float, output: float, effort: bool}> */
    public const MODELS = [
        'claude-opus-5-5' => ['label' => 'Claude Opus 5.5 (chất lượng cao nhất)', 'input' => 4.0, 'output' => 20.0, 'effort' => true],
        'claude-sonnet-5-5' => ['label' => 'Claude Sonnet 5.5 (cân bằng)', 'input' => 2.0, 'output' => 10.0, 'effort' => true],
        'claude-haiku-4-5' => ['label' => 'Claude Haiku 4.5 (rẻ nhất)', 'input' => 1.0, 'output' => 5.0, 'effort' => false],
    ];

    public const EFFORTS = ['low' => 'Thấp (nhanh, rẻ)', 'medium' => 'Vừa', 'high' => 'Cao (chậm, đắt)'];

    /** Typical request: system rules + feed data. */
    private const INPUT_TOKENS = 2500;

    /** Visible JSON article plus thinking tokens, by effort. */
    private const OUTPUT_TOKENS = ['low' => 3500, 'medium' => 5000, 'high' => 8000];

    public static function supportsEffort(string $model): bool
    {
        return self::MODELS[$model]['effort'] ?? false;
    }

    public static function options(): array
    {
        return array_map(fn (array $m) => $m['label'], self::MODELS);
    }

    /** Estimated USD per day for $posts articles (about 30% extra for rejected/retried items). */
    public static function estimateDailyCost(string $model, string $effort, int $posts): float
    {
        $price = self::MODELS[$model] ?? self::MODELS['claude-opus-5-5'];
        $output = self::supportsEffort($model) ? (self::OUTPUT_TOKENS[$effort] ?? self::OUTPUT_TOKENS['low']) : 3000;
        $perPost = (self::INPUT_TOKENS * $price['input'] + $output * $price['output']) / 1_000_000;

        return round($perPost * $posts * 1.3, 2);
    }
}
