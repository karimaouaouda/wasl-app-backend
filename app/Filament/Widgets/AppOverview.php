<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        return [
            $this->makeOrdersStat(),
            $this->makeUsersStat(),
        ];
    }

    private function makeOrdersStat(): Stat
    {
        return Stat::make('Orders', Order::query()->count())
            ->chart([1, 2, 3, 1 ,1.5, 0.5, 3])
            ->description("orders count")
            ->icon('heroicon-o-square-3-stack-3d')
            ->color(Color::Blue);
    }
    private function makeUsersStat(): Stat
    {
        return Stat::make('Delivery representatives', User::query()->where('role', 'user')->count())
            ->chart([1, 2, 3, 1 ,1.5, 0.5, 3])
            ->description("Delivery representatives count")
            ->icon('heroicon-o-users')
            ->color(Color::Green);
    }
}
