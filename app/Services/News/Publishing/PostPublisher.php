<?php

namespace App\Services\News\Publishing;

use App\Enums\PostStatus;
use App\Enums\TypePost;
use App\Models\blogs;
use App\Models\Category;
use App\Models\NewsItem;
use App\Models\SeoDetail;
use App\Models\Tag;
use App\Models\User;
use App\Services\News\Content\ArticleBodyBuilder;
use App\Services\News\Images\ImageSet;
use App\Services\News\NewsSettings;
use App\Services\News\Writing\ArticleDraft;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/** Saves a validated article as a post (published or pending review) together with its SEO row, taxonomy and item link. */
class PostPublisher
{
    private const DEFAULT_CATEGORY_EN = ['tin-cong-nghe' => 'Tech News'];

    public function __construct(
        private readonly NewsSettings $settings,
        private readonly ArticleBodyBuilder $bodyBuilder,
    ) {}

    public function publish(NewsItem $item, ArticleDraft $draft, ImageSet $images): blogs
    {
        $publish = $this->settings->get('mode') !== 'draft';
        $authorId = $this->authorId();

        return DB::transaction(function () use ($item, $draft, $images, $publish, $authorId) {
            $post = blogs::create([
                'title' => $draft->title,
                'slug' => $this->uniqueSlug($draft->slug),
                'sub_title' => Str::limit($draft->metaDescription, 250, ''),
                'body' => $this->bodyBuilder->build($draft->bodyHtml, $item, $images),
                'status' => $publish ? PostStatus::PUBLISHED : PostStatus::PENDING,
                'is_published' => $publish,
                'published_at' => now(),
                'cover_photo_path' => $images->cover->path,
                'photo_alt_text' => Str::limit($images->cover->alt, 250, ''),
                'user_id' => $authorId,
                'type' => TypePost::NEWS->value,
            ]);

            $post->categories()->sync([$this->category($item)->id]);
            $post->tags()->sync(collect($draft->tags)->map(fn (string $name) => $this->tag($name)->id)->all());

            SeoDetail::create([
                'post_id' => $post->id,
                'title' => $draft->seoTitle,
                'keywords' => $draft->keywords,
                'description' => $draft->metaDescription,
            ]);

            $item->forceFill(['status' => NewsItem::STATUS_PUBLISHED, 'post_id' => $post->id, 'error' => null])->save();

            return $post;
        });
    }

    private function authorId(): int
    {
        $configured = $this->settings->get('author_user_id');
        if ($configured && User::whereKey($configured)->exists()) {
            return (int) $configured;
        }

        $first = User::orderBy('id')->value('id');
        if (! $first) {
            throw new RuntimeException('Không có tài khoản quản trị nào để đặt làm tác giả.');
        }

        return (int) $first;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        for ($i = 2; blogs::where('slug', $slug)->exists(); $i++) {
            $slug = Str::limit($base, 140, '').'-'.$i;
        }

        return $slug;
    }

    private function category(NewsItem $item): Category
    {
        if ($item->source?->category) {
            return $item->source->category;
        }

        $name = trim((string) $this->settings->get('default_category')) ?: 'Tin công nghệ';
        $slug = Str::slug($name);

        return Category::where('slug', $slug)->orWhere('name', $name)->first()
            ?? Category::create([
                'name' => $name,
                'slug' => $slug,
                'name_en' => $this->unique('categories', 'name_en', self::DEFAULT_CATEGORY_EN[$slug] ?? Str::headline(Str::ascii($name))),
                'slug_en' => $this->unique('categories', 'slug_en', Str::slug(self::DEFAULT_CATEGORY_EN[$slug] ?? Str::ascii($name))),
            ]);
    }

    private function tag(string $name): Tag
    {
        $slug = Str::slug($name) ?: Str::random(6);

        return Tag::where('slug', $slug)->first()
            ?? Tag::create([
                'name' => $this->unique('tags', 'name', $name),
                'slug' => $slug,
                'name_en' => $this->unique('tags', 'name_en', Str::ascii($name)),
                'slug_en' => $this->unique('tags', 'slug_en', $slug),
            ]);
    }

    /** Appends a counter until the value is free in a UNIQUE column. */
    private function unique(string $table, string $column, string $value): string
    {
        $candidate = $value;
        for ($i = 2; DB::table($table)->where($column, $candidate)->exists(); $i++) {
            $candidate = $value.' '.$i;
        }

        return $column === 'slug_en' ? Str::slug($candidate) : $candidate;
    }
}
