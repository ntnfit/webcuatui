<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'E-commerce Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Name'),
                        
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email'),
                        
                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->label('Phone Number'),
                        
                        Forms\Components\Textarea::make('shipping_address')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Shipping Address'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('₫')
                            ->label('Total Amount'),
                        
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'new' => 'New',
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'unpaid' => 'Unpaid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('new')
                            ->label('Order Status'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->required()
                            ->options([
                                'cod' => 'Cash on Delivery',
                                'bank_transfer' => 'Bank Transfer',
                                'e_wallet' => 'E-Wallet',
                            ])
                            ->default('cod')
                            ->label('Payment Method'),
                        
                        Forms\Components\TextInput::make('user_id')
                            ->numeric()
                            ->label('User ID (Optional)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->label('Customer'),
                
                Tables\Columns\TextColumn::make('customer_email')
                    ->searchable()
                    ->toggleable()
                    ->label('Email'),
                
                Tables\Columns\TextColumn::make('customer_phone')
                    ->searchable()
                    ->toggleable()
                    ->label('Phone'),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable()
                    ->label('Total'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'new',
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'gray' => 'cancelled',
                    ])
                    ->label('Status'),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cod' => 'COD',
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                        default => $state,
                    })
                    ->toggleable()
                    ->label('Payment'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Order Date'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
