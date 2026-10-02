<?php

use App\Enums\PostStatus;
use App\Enums\TypePost;
use App\Filament\Resources\NewsItems\Pages\ListNewsItems;
use App\Filament\Resources\NewsRuns\NewsRunResource;
use App\Filament\Resources\NewsRuns\Pages\ListNewsRuns;
use App\Filament\Resources\NewsSources\Pages\CreateNewsSource;
use App\Filament\Resources\NewsSources\Pages\ListNewsSources;
use App\Models\blogs;
use App\Models\NewsItem;
use App\Models\NewsRun;
use App\Models\NewsSource;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->create();
    $this->actingAs($this->admin);
});

function newsPublishedPair(): array
{
    $post = blogs::create([
        'title' => 'Bài tự động', 'slug' => 'bai-tu-dong', 'body' => '<p>x</p>', 'status' => PostStatus::PUBLISHED, 'is_published' => true,
        'published_at' => now(), 'cover_photo_path' => 'news/a.webp', 'photo_alt_text' => 'a', 'user_id' => User::first()->id, 'type' => TypePost::NEWS->value,
    ]);
    $item = NewsItem::factory()->for(NewsSource::factory(), 'source')->create(['status' => NewsItem::STATUS_PUBLISHED, 'post_id' => $post->id]);

    return [$post, $item];
}

it('takes a published post down from the item list and hides the action afterwards', function () {
    [$post, $item] = newsPublishedPair();

    Livewire::test(ListNewsItems::class)
        ->assertTableActionVisible('unpublish', $item)
        ->callTableAction('unpublish', $item)
        ->assertNotified('Đã gỡ bài')
        ->assertTableActionHidden('unpublish', $item);

    $post->refresh();
    expect($post->status)->toBe(PostStatus::PENDING)->and($post->is_published)->toBeFalsy()
        ->and($item->fresh()->post_id)->toBe($post->id);
});

it('has no takedown action for items without a post', function () {
    $item = NewsItem::factory()->for(NewsSource::factory(), 'source')->create(['status' => NewsItem::STATUS_REJECTED]);

    Livewire::test(ListNewsItems::class)->assertTableActionHidden('unpublish', $item);
});

it('filters items by status', function () {
    $source = NewsSource::factory()->create();
    $failed = NewsItem::factory()->for($source, 'source')->create(['status' => NewsItem::STATUS_FAILED, 'error' => 'API down']);
    $new = NewsItem::factory()->for($source, 'source')->create(['status' => NewsItem::STATUS_NEW]);

    Livewire::test(ListNewsItems::class)
        ->assertCanSeeTableRecords([$failed, $new])
        ->filterTable('status', NewsItem::STATUS_FAILED)
        ->assertCanSeeTableRecords([$failed])
        ->assertCanNotSeeTableRecords([$new]);
});

it('shows the run log read only', function () {
    $run = NewsRun::factory()->create(['published' => 7, 'notes' => 'ghi chú']);

    Livewire::test(ListNewsRuns::class)->assertCanSeeTableRecords([$run])->assertSee('ghi chú');

    expect(NewsRunResource::canCreate())->toBeFalse()
        ->and(NewsRunResource::canEdit($run))->toBeFalse()
        ->and(NewsRunResource::canDelete($run))->toBeFalse()
        ->and(array_keys(NewsRunResource::getPages()))->toBe(['index']);
});

it('manages sources: create, toggle, and see the last error', function () {
    Livewire::test(CreateNewsSource::class)
        ->fillForm(['name' => 'Feed mới', 'type' => 'atom', 'url' => 'https://example.test/atom.xml', 'language' => 'en', 'weight' => 3, 'enabled' => true])
        ->call('create')
        ->assertHasNoFormErrors();

    $source = NewsSource::where('url', 'https://example.test/atom.xml')->firstOrFail();
    expect($source->type)->toBe('atom')->and($source->weight)->toBe(3);

    $source->update(['last_error' => 'HTTP 503']);

    Livewire::test(ListNewsSources::class)
        ->assertCanSeeTableRecords([$source])
        ->assertSee('HTTP 503')
        ->call('updateTableColumnState', 'enabled', $source->getKey(), false);

    expect($source->fresh()->enabled)->toBeFalse();
});

it('validates the feed url and rejects duplicates', function () {
    NewsSource::factory()->create(['url' => 'https://example.test/dup.xml']);

    Livewire::test(CreateNewsSource::class)
        ->fillForm(['name' => 'x', 'type' => 'rss', 'url' => 'not a url', 'language' => 'vi', 'weight' => 1])
        ->call('create')
        ->assertHasFormErrors(['url']);

    Livewire::test(CreateNewsSource::class)
        ->fillForm(['name' => 'x', 'type' => 'rss', 'url' => 'https://example.test/dup.xml', 'language' => 'vi', 'weight' => 1])
        ->call('create')
        ->assertHasFormErrors(['url' => 'unique']);
});
