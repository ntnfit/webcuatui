<?php

namespace App\Filament\Resources\Orders;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Orders\RelationManagers\OrderDetailsRelationManager;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'E-commerce Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Full Name'),
                        
                        TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email'),
                        
                        TextInput::make('customer_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->label('Phone Number'),
                        
                        Textarea::make('shipping_address')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Shipping Address'),
                    ])
                    ->columns(2),
                
                Section::make('Order Details')
                    ->schema([
                        TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('₫')
                            ->label('Total Amount'),
                        
                        Select::make('status')
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
                        
                        Select::make('payment_method')
                            ->required()
                            ->options([
                                'cod' => 'Cash on Delivery',
                                'bank_transfer' => 'Bank Transfer',
                                'e_wallet' => 'E-Wallet',
                            ])
                            ->default('cod')
                            ->label('Payment Method'),
                        
                        TextInput::make('user_id')
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
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('customer_name')
                    ->searchable()
                    ->label('Customer'),
                
                TextColumn::make('customer_email')
                    ->searchable()
                    ->toggleable()
                    ->label('Email'),
                
                TextColumn::make('customer_phone')
                    ->searchable()
                    ->toggleable()
                    ->label('Phone'),
                
                TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable()
                    ->label('Total'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'new',
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'gray' => 'cancelled',
                    ])
                    ->label('Status'),
                
                TextColumn::make('payment_method')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cod' => 'COD',
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                        default => $state,
                    })
                    ->toggleable()
                    ->label('Payment'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Order Date'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            OrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
