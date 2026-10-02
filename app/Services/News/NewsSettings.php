<?php

namespace App\Services\News;

use App\Models\NewsSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;
use Throwable;

/**
 * Typed access to the news auto-poster settings.
 *
 * Resolution order: database override (admin page) > config/news.php (which reads env).
 * API keys live in the same table encrypted with the application key; the env value
 * is only the fallback when no key was saved from the admin page.
 */
class NewsSettings
{
    public const SECRETS = ['anthropic', 'unsplash', 'pexels'];

    private const CACHE_KEY = 'news.settings.rows';

    /** @var array<string, array{value: ?string, is_secret: bool}>|null */
    private ?array $rows = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $rows = $this->rows();

        if (isset($rows[$key]) && ! $rows[$key]['is_secret'] && $rows[$key]['value'] !== null) {
            return json_decode($rows[$key]['value'], true);
        }

        return config('news.'.$key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        if (! Arr::has(config('news'), $key)) {
            throw new InvalidArgumentException("Unknown news setting [{$key}].");
        }

        $this->store($key, json_encode($value, JSON_UNESCAPED_UNICODE), false);
    }

    /** Drop the database override so the config/env default applies again. */
    public function reset(string $key): void
    {
        NewsSetting::where('key', $key)->delete();
        $this->flush();
    }

    public function enabled(): bool
    {
        return (bool) $this->get('enabled');
    }

    public function secret(string $name): ?string
    {
        $this->assertSecret($name);
        $row = $this->rows()['secret.'.$name] ?? null;

        if ($row && $row['value'] !== null) {
            try {
                return Crypt::decryptString($row['value']);
            } catch (DecryptException) {
                // Key rotated or row corrupted: behave as "not saved" and fall back to env.
            }
        }

        $fallback = config('news.keys.'.$name);

        return is_string($fallback) && $fallback !== '' ? $fallback : null;
    }

    /** where the usable key comes from: db, env or null when missing. */
    public function secretSource(string $name): ?string
    {
        $this->assertSecret($name);
        $row = $this->rows()['secret.'.$name] ?? null;

        if ($row && $row['value'] !== null) {
            try {
                Crypt::decryptString($row['value']);

                return 'db';
            } catch (DecryptException) {
            }
        }

        $fallback = config('news.keys.'.$name);

        return is_string($fallback) && $fallback !== '' ? 'env' : null;
    }

    public function setSecret(string $name, string $value): void
    {
        $this->assertSecret($name);
        $this->store('secret.'.$name, Crypt::encryptString(trim($value)), true);
    }

    public function clearSecret(string $name): void
    {
        $this->assertSecret($name);
        $this->reset('secret.'.$name);
    }

    private function store(string $key, ?string $value, bool $secret): void
    {
        NewsSetting::updateOrCreate(['key' => $key], ['value' => $value, 'is_secret' => $secret]);
        $this->flush();
    }

    private function assertSecret(string $name): void
    {
        if (! in_array($name, self::SECRETS, true)) {
            throw new InvalidArgumentException("Unknown news secret [{$name}].");
        }
    }

    private function flush(): void
    {
        $this->rows = null;
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, array{value: ?string, is_secret: bool}> */
    private function rows(): array
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        try {
            return $this->rows = Cache::rememberForever(self::CACHE_KEY, fn () => NewsSetting::query()
                ->get(['key', 'value', 'is_secret'])
                ->mapWithKeys(fn (NewsSetting $row) => [$row->key => [
                    'value' => $row->getAttributes()['value'] ?? null,
                    'is_secret' => (bool) $row->is_secret,
                ]])
                ->all());
        } catch (Throwable) {
            // Table not migrated yet (fresh deploy): config defaults apply.
            return $this->rows = [];
        }
    }
}
