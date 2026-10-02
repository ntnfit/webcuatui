<?php

namespace App\Filament\Customer\Resources\TaxExportRuns;

use App\Filament\Customer\Resources\TaxExportRuns\Pages\ListTaxExportRuns;
use App\Models\TaxExportRun;
use App\Services\Gdt\Export\ExportRunner;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/** Export history of the active company with authorised downloads. */
class TaxExportRunResource extends Resource
{
    protected static ?string $model = TaxExportRun::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?string $navigationLabel = 'Lịch sử xuất file';

    protected static ?string $modelLabel = 'lượt xuất file';

    protected static ?string $pluralModelLabel = 'lịch sử xuất file';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('5s')
            ->columns([
                TextColumn::make('created_at')->label('Thời gian')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('type')->label('Loại')->badge()
                    ->formatStateUsing(fn (string $state) => $state === TaxExportRun::TYPE_EXCEL ? 'Excel' : 'XML (zip)'),
                TextColumn::make('filters')->label('Kỳ')
                    ->state(fn (TaxExportRun $r) => ($r->filters['direction'] === 'sold' ? 'Bán ra' : 'Mua vào')
                        .' '.$r->filters['from'].' → '.$r->filters['to']),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (string $state) => [
                        TaxExportRun::STATUS_PENDING => 'Đang chờ',
                        TaxExportRun::STATUS_RUNNING => 'Đang xử lý',
                        TaxExportRun::STATUS_DONE => 'Hoàn tất',
                        TaxExportRun::STATUS_FAILED => 'Thất bại',
                    ][$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        TaxExportRun::STATUS_DONE => 'success',
                        TaxExportRun::STATUS_FAILED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('error')->label('Ghi chú')->wrap()->limit(80),
                TextColumn::make('expires_at')->label('Hết hạn tải')->dateTime('d/m/Y')->placeholder('—'),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->authorize('view')
                    ->visible(fn (TaxExportRun $record) => $record->isDownloadable() && $record->company->hasValidLicense())
                    ->action(function (TaxExportRun $record) {
                        $disk = Storage::disk(ExportRunner::DISK);

                        return $disk->exists($record->file_path) ? $disk->download($record->file_path, basename($record->file_path)) : null;
                    }),
                DeleteAction::make()->before(function (TaxExportRun $record) {
                    if ($record->file_path) {
                        Storage::disk(ExportRunner::DISK)->delete($record->file_path);
                    }
                }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxExportRuns::route('/'),
        ];
    }
}
