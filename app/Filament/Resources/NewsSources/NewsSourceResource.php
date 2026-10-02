<?php

namespace App\Filament\Resources\NewsSources;

use App\Filament\Resources\NewsSources\Pages\CreateNewsSource;
use App\Filament\Resources\NewsSources\Pages\EditNewsSource;
use App\Filament\Resources\NewsSources\Pages\ListNewsSources;
use App\Models\NewsSource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsSourceResource extends Resource
{
    protected static ?string $model = NewsSource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rss';

    protected static string|\UnitEnum|null $navigationGroup = 'Tin tự động';

    protected static ?string $navigationLabel = 'Nguồn tin';

    protected static ?string $modelLabel = 'nguồn tin';

    protected static ?string $pluralModelLabel = 'nguồn tin';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Tên nguồn')->required()->maxLength(255),
            Select::make('type')->label('Loại feed')->options(['rss' => 'RSS', 'atom' => 'Atom'])->default('rss')->required(),
            TextInput::make('url')->label('URL feed (RSS/Atom, không phải trang HTML)')->url()->required()->maxLength(500)
                ->unique(ignoreRecord: true)->columnSpanFull(),
            Select::make('language')->label('Ngôn ngữ')->options(['vi' => 'Tiếng Việt', 'en' => 'Tiếng Anh'])->default('en')->required(),
            Select::make('category_id')->label('Danh mục mặc định cho bài từ nguồn này')->relationship('category', 'name')->searchable()->preload()->nullable(),
            TextInput::make('weight')->label('Trọng số (điểm ưu tiên)')->numeric()->minValue(0)->maxValue(10)->default(1)->required(),
            Toggle::make('enabled')->label('Đang bật')->default(true),
            DateTimePicker::make('last_fetched_at')->label('Lần lấy tin gần nhất')->disabled()->dehydrated(false),
            Textarea::make('last_error')->label('Lỗi gần nhất')->disabled()->dehydrated(false)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Tên nguồn')->searchable()->sortable(),
                TextColumn::make('type')->label('Loại')->badge(),
                TextColumn::make('language')->label('Ngôn ngữ')->badge(),
                TextColumn::make('weight')->label('Trọng số')->sortable(),
                ToggleColumn::make('enabled')->label('Bật'),
                TextColumn::make('last_fetched_at')->label('Lấy tin lúc')->since()->sortable(),
                TextColumn::make('last_error')->label('Lỗi gần nhất')->limit(60)->tooltip(fn (NewsSource $record) => $record->last_error)->color('danger'),
            ])
            ->filters([
                TernaryFilter::make('enabled')->label('Trạng thái bật'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsSources::route('/'),
            'create' => CreateNewsSource::route('/create'),
            'edit' => EditNewsSource::route('/{record}/edit'),
        ];
    }
}
