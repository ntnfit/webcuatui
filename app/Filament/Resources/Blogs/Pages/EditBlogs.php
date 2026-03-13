<?php

namespace App\Filament\Resources\BlogsResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogs extends EditRecord
{
    protected static string $resource = BlogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave()
    {
        if ($this->data['status'] === PostStatus::PUBLISHED->value) {
            $this->record->published_at = $this->record->published_at ?? date('Y-m-d H:i:s');
        }
    }
}
