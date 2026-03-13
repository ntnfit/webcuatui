<?php

namespace App\Filament\Resources\Blogs;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use App\Filament\Resources\Blogs\Pages\EditBlogs;
use App\Filament\Resources\Blogs\Pages\ListBlogs;
use App\Filament\Resources\Blogs\Pages\CreateBlogs;
use App\Enums\PostStatus;
use App\Filament\Resources\BlogsResource\Pages;
use App\Filament\Resources\Blogs\Pages\ManagePostSeoDetail;
use App\Filament\Resources\Blogs\Pages\ViewPost;
use App\Filament\Resources\Blogs\Widgets\BlogPostPublishedChart;
use App\Models\blogs as Post;
use App\Tables\Columns\UserPhotoName;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogsResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-minus';

    protected static $title = 'Blogs';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | \UnitEnum | null $navigationGroup = 'Blog Management';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return Post::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                Post::getForm()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                TextColumn::make('title')
                    ->description(function (Post $record) {
                        return Str::limit($record->sub_title, 40);
                    })
                    ->searchable()->limit(20),
                TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                ImageColumn::make('cover_photo_path')->label('Cover Photo'),

                UserPhotoName::make('user')
                    ->label('Author'),
                ToggleColumn::make('is_published')
                    ->label('Publish')
                    ->updateStateUsing(function (post $record, $state) {
                        $record->is_published = $state;
                        if ($state == true) {
                            $record->status = PostStatus::PUBLISHED;
                            $record->published_at = now();
                        } else {
                            $record->status = PostStatus::PENDING;
                            $record->published_at = null;
                        }
                        $record->save();
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post')
                ->schema([
                    Fieldset::make('General')
                        ->schema([
                            TextEntry::make('title'),
                            TextEntry::make('slug'),
                            TextEntry::make('sub_title'),
                        ]),
                    Fieldset::make('Publish Information')
                        ->schema([
                            TextEntry::make('status')
                                ->badge()->color(function ($state) {
                                    return $state->getColor();
                                }),
                            TextEntry::make('published_at')->visible(function (Post $record) {
                                return $record->status === PostStatus::PUBLISHED;
                            }),

                            //                            TextEntry::make('scheduled_for')->visible(function (Post $record) {
                            //                                return $record->status === PostStatus::SCHEDULED;
                            //                            }),
                        ]),
                    Fieldset::make('Description')
                        ->schema([
                            TextEntry::make('body')
                                ->html()
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPost::class,
            ManagePostSeoDetail::class,
            //            ManagePostComments::class,
            EditBlogs::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BlogPostPublishedChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogs::route('/'),
            'create' => CreateBlogs::route('/create'),
            'edit' => EditBlogs::route('/{record}/edit'),
            'view' => ViewPost::route('/{record}'),
            'seoDetail' => ManagePostSeoDetail::route('/{record}/seo-details'),
        ];
    }
}
