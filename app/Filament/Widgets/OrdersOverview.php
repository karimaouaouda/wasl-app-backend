<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class OrdersOverview extends ChartWidget
{
    protected static ?string $heading = 'order creation from start o month';

    protected static ?string $description = "the number of orders created from start month";

    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $users = Order::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, count(*) as count')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('date')
            ->get()->pluck('count', 'date')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'backgroundColor' => ['rgb(255, 99, 132)'],
                    'borderColor' => ['rgb(255, 99, 132)'],
                    'data' => $users
                ]
            ],
            'labels' => array_keys($users),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
