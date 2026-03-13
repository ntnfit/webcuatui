<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'E-commerce Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Coupon Code')
                    ->placeholder('e.g., SAVE10'),
                
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'fixed' => 'Fixed Amount (VND)',
                        'percent' => 'Percentage (%)',
                    ])
                    ->default('fixed')
                    ->live()
                    ->label('Discount Type'),
                
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->label(fn ($get) => $get('type') === 'percent' ? 'Discount Percentage (%)' : 'Discount Amount (VND)')
                    ->placeholder(fn ($get) => $get('type') === 'percent' ? 'e.g., 10' : 'e.g., 50000')
                    ->suffix(fn ($get) => $get('type') === 'percent' ? '%' : '₫'),
                
                Forms\Components\TextInput::make('max_discount')
                    ->numeric()
                    ->label('Maximum Discount (VND)')
                    ->helperText('For percentage coupons. 0 = no limit')
                    ->default(0)
                    ->suffix('₫')
                    ->visible(fn ($get) => $get('type') === 'percent'),
                
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(-1)
                    ->label('Quantity')
                    ->helperText('-1 = unlimited'),
                
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->native(false),
                
                Forms\Components\Select::make('status')
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
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->label('Coupon Code'),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'fixed',
                        'warning' => 'percent',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'fixed' ? 'Fixed' : 'Percentage')
                    ->label('Type'),
                
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percent' ? $record->value . '%' : number_format($record->value, 0, ',', '.') . '₫')
                    ->label('Discount'),
                
                Tables\Columns\TextColumn::make('max_discount')
                    ->money('VND')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'No limit' : number_format($state, 0, ',', '.') . '₫')
                    ->label('Max Discount')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->formatStateUsing(fn ($state) => $state == -1 ? 'Unlimited' : $state)
                    ->label('Quantity')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->label('Expires'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->label('Status'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
