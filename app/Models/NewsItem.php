<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One feed entry seen by the news auto-poster and what happened to it. */
class NewsItem extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_SELECTED = 'selected';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FAILED = 'failed';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class, 'source_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(blogs::class, 'post_id');
    }

    /** Stable identity of an entry: its guid when the feed has one, otherwise the URL. */
    public static function hashFor(string $guidOrUrl): string
    {
        return sha1(trim($guidOrUrl));
    }
}
