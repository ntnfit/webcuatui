<?php

namespace App\Filament\Resources\Coupons;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'E-commerce Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Coupon Code')
                    ->placeholder('e.g., SAVE10'),
                
                Select::make('type')
                    ->required()
                    ->options([
                        'fixed' => 'Fixed Amount (VND)',
                        'percent' => 'Percentage (%)',
                    ])
                    ->default('fixed')
                    ->live()
                    ->label('Discount Type'),
                
                TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->label(fn ($get) => $get('type') === 'percent' ? 'Discount Percentage (%)' : 'Discount Amount (VND)')
                    ->placeholder(fn ($get) => $get('type') === 'percent' ? 'e.g., 10' : 'e.g., 50000')
                    ->suffix(fn ($get) => $get('type') === 'percent' ? '%' : '₫'),
                
                TextInput::make('max_discount')
                    ->numeric()
                    ->label('Maximum Discount (VND)')
                    ->helperText('For percentage coupons. 0 = no limit')
                    ->default(0)
                    ->suffix('₫')
                    ->visible(fn ($get) => $get('type') === 'percent'),
                
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(-1)
                    ->label('Quantity')
                    ->helperText('-1 = unlimited'),
                
                DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->native(false),
                
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->label('Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->label('Coupon Code'),
                
                BadgeColumn::make('type')
                    ->colors([
                        'success' => 'fixed',
                        'warning' => 'percent',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'fixed' ? 'Fixed' : 'Percentage')
                    ->label('Type'),
                
                TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percent' ? $record->value . '%' : number_format($record->value, 0, ',', '.') . '₫')
                    ->label('Discount'),
                
                TextColumn::make('max_discount')
                    ->money('VND')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'No limit' : number_format($state, 0, ',', '.') . '₫')
                    ->label('Max Discount')
                    ->toggleable(),
                
                TextColumn::make('quantity')
                    ->formatStateUsing(fn ($state) => $state == -1 ? 'Unlimited' : $state)
                    ->label('Quantity')
                    ->sortable(),
                
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->label('Expires'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->label('Status'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
