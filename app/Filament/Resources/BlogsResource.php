<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogsResource\Pages;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\blogs as Post;
use Illuminate\Support\Str;
use App\Tables\Columns\UserPhotoName;
use App\Enums\PostStatus;
use App\Filament\Resources\BlogsResource\Pages\ViewPost;
use App\Filament\Resources\BlogsResource\Pages\ManagePostSeoDetail;
use App\Filament\Resources\BlogsResource\Widgets\BlogPostPublishedChart;
class BlogsResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';
    protected static $title = 'Blogs';
    protected static ?string $recordTitleAttribute = 'title';
    protected static  ?string $navigationGroup = 'Blog Management';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    public static function getNavigationBadge(): ?string
    {
        return Post::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                Post::getForm()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(function (Post $record) {
                        return Str::limit($record->sub_title, 40);
                    })
                    ->searchable()->limit(20),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\ImageColumn::make('cover_photo_path')->label('Cover Photo'),

                UserPhotoName::make('user')
                    ->label('Author'),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Publish')
                    ->updateStateUsing(function (post $record,$state) {
                        $record->is_published = $state;
                        if($state==true){
                            $record->status = PostStatus::PUBLISHED;
                            $record->published_at = now();
                        }
                        else{
                            $record->status = PostStatus::PENDING;
                            $record->published_at = null;
                        }
                       $record->save();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
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
            Pages\EditBlogs::class,
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlogs::route('/create'),
            'edit' => Pages\EditBlogs::route('/{record}/edit'),
            'view' =>   Pages\ViewPost::route('/{record}'),
            'seoDetail' => Pages\ManagePostSeoDetail::route('/{record}/seo-details'),
        ];
    }

}
