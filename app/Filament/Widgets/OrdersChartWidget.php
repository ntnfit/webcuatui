<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders per month';

    protected function getData(): array
    {
        $data = \App\Models\Order::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyOrders = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyOrders[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $monthlyOrders,
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
