<?php

namespace App\Filament\Resources\BlogsResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogs extends CreateRecord
{
    protected static string $resource = BlogsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->data['status'] === PostStatus::PUBLISHED->value) {
            $data['is_published'] = true;
            $data['published_at'] = date('Y-m-d H:i:s');

            return $data;
        }

    }
}
