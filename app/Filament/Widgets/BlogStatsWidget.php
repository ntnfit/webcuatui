<?php

namespace App\Filament\Widgets;

use App\Models\blogs;
use Illuminate\Support\Str;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $mostViewed = blogs::orderByDesc('view')->first();

        return [
            Stat::make('Total Posts', blogs::count()),
            Stat::make('Most Viewed Post', $mostViewed ? Str::limit($mostViewed->title, 20) : 'N/A')
                ->description($mostViewed ? $mostViewed->view . ' views' : null),
            Stat::make('Total Products', Product::count()),
        ];
    }
}
