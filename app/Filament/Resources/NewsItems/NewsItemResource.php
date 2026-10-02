<?php

namespace App\Filament\Resources\NewsItems;

use App\Enums\PostStatus;
use App\Filament\Resources\NewsItems\Pages\ListNewsItems;
use App\Models\NewsItem;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

/** Every feed entry the bot has seen, with a fast takedown for the posts it created. */
class NewsItemResource extends Resource
{
    protected static ?string $model = NewsItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Tin tự động';

    protected static ?string $navigationLabel = 'Tin đã thu thập';

    protected static ?string $modelLabel = 'tin';

    protected static ?string $pluralModelLabel = 'tin đã thu thập';

    protected static ?int $navigationSort = 3;

    public const STATUS_LABELS = [
        NewsItem::STATUS_NEW => 'Mới',
        NewsItem::STATUS_SELECTED => 'Đã chọn',
        NewsItem::STATUS_PUBLISHED => 'Đã đăng',
        NewsItem::STATUS_REJECTED => 'Bị loại',
        NewsItem::STATUS_FAILED => 'Lỗi',
    ];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Tiêu đề')->limit(70)->tooltip(fn (NewsItem $record) => $record->title)->searchable()->url(fn (NewsItem $record) => $record->url, shouldOpenInNewTab: true),
                TextColumn::make('source.name')->label('Nguồn')->sortable(),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        NewsItem::STATUS_PUBLISHED => 'success',
                        NewsItem::STATUS_REJECTED => 'warning',
                        NewsItem::STATUS_FAILED => 'danger',
                        NewsItem::STATUS_SELECTED => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('score')->label('Điểm')->sortable(),
                TextColumn::make('post.status')->label('Bài viết')->badge()->placeholder('—')
                    ->formatStateUsing(fn ($state) => $state instanceof PostStatus ? ($state === PostStatus::PUBLISHED ? 'Đang hiển thị' : 'Đã gỡ / chờ duyệt') : $state),
                TextColumn::make('error')->label('Lý do / lỗi')->limit(60)->tooltip(fn (NewsItem $record) => $record->error)->placeholder('—'),
                TextColumn::make('published_at')->label('Ngày tin')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(self::STATUS_LABELS),
                SelectFilter::make('source_id')->label('Nguồn')->relationship('source', 'name'),
            ])
            ->recordActions([
                Action::make('unpublish')
                    ->label('Gỡ bài')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Gỡ bài viết khỏi website?')
                    ->modalDescription('Bài chuyển về trạng thái chờ duyệt và không còn hiển thị công khai.')
                    ->visible(fn (NewsItem $record) => $record->post?->status === PostStatus::PUBLISHED)
                    ->action(function (NewsItem $record) {
                        $record->post->update(['status' => PostStatus::PENDING, 'is_published' => false]);
                        Cache::forget('home.latest-articles');
                        Notification::make()->title('Đã gỡ bài')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListNewsItems::route('/')];
    }
}
