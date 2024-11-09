<?php

namespace App\Filament\Resources\ContactsResource\Pages;

use App\Events\CampaignEmailEvent;
use App\Filament\Resources\ContactsResource;
use App\Models\blogs as Post;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
class ListContacts extends ListRecords
{
    protected static string $resource = ContactsResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
            Action::make('sendNotification')
                ->label('Send Campaign')
                ->requiresConfirmation()
                ->icon('heroicon-o-bell')->action(function () {
                    event(new CampaignEmailEvent());
                }),
        ];
    }
}
