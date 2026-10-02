<?php

namespace App\Filament\Resources\NewsRuns\Pages;

use App\Filament\Resources\NewsRuns\NewsRunResource;
use Filament\Resources\Pages\ListRecords;

class ListNewsRuns extends ListRecords
{
    protected static string $resource = NewsRunResource::class;
}
