<?php

namespace App\Filament\Resources\ContactsResource\Pages;

use App\Events\CampaignEmailEvent;
use App\Filament\Resources\ContactsResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

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
                    event(new CampaignEmailEvent);
                }),
        ];
    }
}
