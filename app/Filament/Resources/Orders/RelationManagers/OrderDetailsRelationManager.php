<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    protected static ?string $title = 'Order Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Product'),
                
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->label('Quantity'),
                
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₫')
                    ->label('Unit Price'),
                
                TextInput::make('line_total')
                    ->required()
                    ->numeric()
                    ->prefix('₫')
                    ->label('Line Total'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                
                TextColumn::make('price')
                    ->label('Unit Price')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable(),
                
                TextColumn::make('line_total')
                    ->label('Line Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->formatStateUsing(fn ($state) => 'Total: ' . number_format($state, 0, ',', '.') . '₫'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
