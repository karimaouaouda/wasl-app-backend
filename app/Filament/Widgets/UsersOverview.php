<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UsersOverview extends ChartWidget
{
    protected static ?string $heading = 'users accounts creation';

    protected static ?string $description = "the number of users registered from start month";

    protected static bool $isLazy = false;
    protected function getData(): array
    {
        $users = User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as date, count(*) as count')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('date')
            ->get()->pluck('count', 'date')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'orders',
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
