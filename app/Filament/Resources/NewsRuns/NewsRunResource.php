<?php

namespace App\Filament\Resources\NewsRuns;

use App\Filament\Resources\NewsRuns\Pages\ListNewsRuns;
use App\Models\NewsRun;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Read-only log of every news auto-poster execution. */
class NewsRunResource extends Resource
{
    protected static ?string $model = NewsRun::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Tin tự động';

    protected static ?string $navigationLabel = 'Nhật ký chạy';

    protected static ?string $modelLabel = 'lượt chạy';

    protected static ?string $pluralModelLabel = 'lượt chạy';

    protected static ?int $navigationSort = 4;

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
            ->defaultSort('started_at', 'desc')
            ->columns([
                TextColumn::make('started_at')->label('Bắt đầu')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('finished_at')->label('Kết thúc')->dateTime('d/m/Y H:i')->placeholder('—'),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        NewsRun::STATUS_RUNNING => 'Đang chạy',
                        NewsRun::STATUS_COMPLETED => 'Hoàn tất',
                        default => 'Dừng sớm',
                    })
                    ->color(fn (string $state) => match ($state) {
                        NewsRun::STATUS_RUNNING => 'warning',
                        NewsRun::STATUS_COMPLETED => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('fetched')->label('Lấy về'),
                TextColumn::make('selected')->label('Đã chọn'),
                TextColumn::make('published')->label('Đã đăng'),
                TextColumn::make('rejected')->label('Bị loại'),
                TextColumn::make('failed')->label('Lỗi'),
                TextColumn::make('notes')->label('Ghi chú')->limit(80)->tooltip(fn (NewsRun $record) => $record->notes)->wrap(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListNewsRuns::route('/')];
    }
}
