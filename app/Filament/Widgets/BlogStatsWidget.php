<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $mostViewed = \App\Models\blogs::orderByDesc('view')->first();

        return [
            Stat::make('Total Posts', \App\Models\blogs::count()),
            Stat::make('Most Viewed Post', $mostViewed ? \Illuminate\Support\Str::limit($mostViewed->title, 20) : 'N/A')
                ->description($mostViewed ? $mostViewed->view . ' views' : null),
            Stat::make('Total Products', \App\Models\Product::count()),
        ];
    }
}
