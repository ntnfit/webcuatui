<?php

namespace App\Filament\Resources\BlogsResource\Pages;

use App\Filament\Resources\BlogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BlogsResource\Widgets\BlogPostPublishedChart;
use Filament\Resources\Components\Tab;

class ListBlogs extends ListRecords
{
    protected static string $resource = BlogsResource::class;

    protected function getColumns(): int | array
    {
        return 2;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BlogPostPublishedChart::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(function ($query) {
                    $query->published();
                })->icon('heroicon-o-check-badge'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(function ($query) {
                    $query->pending();
                })
                ->icon('heroicon-o-clock'),
//            'scheduled' => Tab::make('Scheduled')
//                ->modifyQueryUsing(function ($query) {
//                    $query->scheduled();
//                })
//                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
