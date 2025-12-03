<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    protected static ?string $title = 'Order Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Product'),
                
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->label('Quantity'),
                
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₫')
                    ->label('Unit Price'),
                
                Forms\Components\TextInput::make('line_total')
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
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('line_total')
                    ->label('Line Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn ($state) => 'Total: ' . number_format($state, 0, ',', '.') . '₫'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
