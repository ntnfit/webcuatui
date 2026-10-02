<?php

namespace App\Filament\Resources\TaxLicenses;

use App\Filament\Resources\TaxLicenses\Pages\CreateTaxLicense;
use App\Filament\Resources\TaxLicenses\Pages\EditTaxLicense;
use App\Filament\Resources\TaxLicenses\Pages\ListTaxLicenses;
use App\Models\Company;
use App\Models\TaxLicense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/** Admin-only management of Tax licenses (assign, extend with a new row, revoke). */
class TaxLicenseResource extends Resource
{
    protected static ?string $model = TaxLicense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Tax';

    protected static ?string $navigationLabel = 'Giấy phép Tax';

    protected static ?string $modelLabel = 'giấy phép Tax';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label('Công ty')
                ->relationship('company', 'name')
                ->getOptionLabelFromRecordUsing(fn (Company $company) => "{$company->name} — {$company->mst}")
                ->searchable(['name', 'mst'])
                ->preload()
                ->required(),
            DatePicker::make('starts_at')->label('Bắt đầu')->native(false)->required()->default(now())
                ->dehydrateStateUsing(fn ($state) => Carbon::parse($state)->startOfDay()),
            DatePicker::make('expires_at')->label('Hết hạn')->native(false)->required()->afterOrEqual('starts_at')
                ->dehydrateStateUsing(fn ($state) => Carbon::parse($state)->endOfDay()),
            Select::make('status')->label('Trạng thái')->options([
                TaxLicense::STATUS_ACTIVE => 'Đang hoạt động',
                TaxLicense::STATUS_REVOKED => 'Đã thu hồi',
            ])->default(TaxLicense::STATUS_ACTIVE)->required(),
            Textarea::make('notes')->label('Ghi chú')->rows(3)->maxLength(1000)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('expires_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('company'))
            ->columns([
                TextColumn::make('company.name')->label('Công ty')->searchable()->sortable(),
                TextColumn::make('company.mst')->label('MST')->searchable(),
                TextColumn::make('expires_at')->label('Hết hạn')->date('d/m/Y')->sortable(),
                TextColumn::make('days_left')->label('Còn lại')
                    ->state(fn (TaxLicense $r) => $r->isValid() ? (int) ceil(now()->diffInDays($r->expires_at, false)).' ngày' : '—'),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->state(fn (TaxLicense $r) => match (true) {
                        $r->status === TaxLicense::STATUS_REVOKED => 'Đã thu hồi',
                        $r->expires_at->isPast() => 'Hết hạn',
                        $r->starts_at->isFuture() => 'Chưa bắt đầu',
                        default => 'Hiệu lực',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Hiệu lực' => 'success',
                        'Chưa bắt đầu' => 'info',
                        default => 'danger',
                    }),
                TextColumn::make('notes')->label('Ghi chú')->limit(40)->toggleable(),
            ])
            ->filters([
                Filter::make('expiring')->label('Sắp hết hạn (30 ngày)')->toggle()->query(fn (Builder $query) => $query
                    ->where('status', TaxLicense::STATUS_ACTIVE)
                    ->whereBetween('expires_at', [now(), now()->addDays(30)])),
                Filter::make('expired')->label('Đã hết hạn')->toggle()->query(fn (Builder $query) => $query->where('expires_at', '<', now())),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxLicenses::route('/'),
            'create' => CreateTaxLicense::route('/create'),
            'edit' => EditTaxLicense::route('/{record}/edit'),
        ];
    }
}
