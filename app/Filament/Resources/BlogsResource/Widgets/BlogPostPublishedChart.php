<?php

namespace App\Filament\Resources\BlogsResource\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\blogs as Post;
class BlogPostPublishedChart extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    public function getColumns(): int



    {
        return 2;
    }
    protected function getStats(): array
    {
        //   BaseWidget\Stat::make('Scheduled Post', Post::scheduled()->count()),
        return [
            BaseWidget\Stat::make('Published Post', Post::published()->count())
                ->icon('heroicon-o-document-text')
                ->color('success')
            ,
            BaseWidget\Stat::make('Pending Post', Post::pending()->count())
                ->icon('heroicon-o-clock')
              ,
        ];
    }
}
