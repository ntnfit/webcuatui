<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CustomersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Total customers';

    protected function getData(): array
    {
        $data = \App\Models\Customer::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyCustomers = [];
        $cumulative = 0;
        for ($i = 1; $i <= 12; $i++) {
            $count = $data[$i] ?? 0;
            $cumulative += $count;
            $monthlyCustomers[] = $cumulative;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $monthlyCustomers,
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
