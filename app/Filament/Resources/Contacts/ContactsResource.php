<?php

namespace App\Filament\Resources\Contacts;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\ContactsResource\Pages;
use App\Models\Contacts;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ContactsResource extends Resource 
{
    use InteractsWithActions;
    protected static ?string $model = Contacts::class;

    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Contacts';

    public static function getNavigationBadge(): ?string
    {
        return Contacts::count();
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Contacts::with('contactReason')->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('topic')
                    ->label('Topic')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Message')
                    ->searchable(),
                TextColumn::make('contactReason.name')
                    ->label('Contact Reason')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make(),
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
            'index' => ListContacts::route('/'),
            // 'create' => Pages\CreateContacts::route('/create'),
            // 'edit' => Pages\EditContacts::route('/{record}/edit'),
        ];
    }
}
