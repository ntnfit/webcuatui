<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactsResource\Pages;
use App\Filament\Resources\ContactsResource\RelationManagers;
use App\Models\Contacts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\MaxWidth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ContactsResource extends Resource
{
    protected static ?string $model = Contacts::class;

    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Contacts';
    public static function getNavigationBadge(): ?string
    {
        return Contacts::count();
    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Contact::with('contactReason')->orderBy('created_at', 'desc'))
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
                TextColumn::make('company')
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
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
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
            'index' => Pages\ListContacts::route('/'),
            //'create' => Pages\CreateContacts::route('/create'),
            // 'edit' => Pages\EditContacts::route('/{record}/edit'),
        ];
    }
}
