<?php

namespace App\Filament\Resources\Blogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Blogs\BlogsResource;
use App\Filament\Resources\Blogs\Widgets\BlogPostPublishedChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogs extends ListRecords
{
    protected static string $resource = BlogsResource::class;

    protected function getColumns(): int|array
    {
        return 2;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
