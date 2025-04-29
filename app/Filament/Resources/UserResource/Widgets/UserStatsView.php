<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use function Filament\Support\generate_href_html;

class UserStatsView extends BaseWidget
{
    public User $user;

    public Collection $orders;

    protected static ?string $pollingInterval = '60s';

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $this->orders = $this->user->orders()->get();
        return [
            $this->makeAllOrdersStat(),
            $this->makeFinishedOrdersStat(),
            $this->makeRejectedORdersStat()
        ];
    }

    private function makeAllOrdersStat(): Stat
    {
        return Stat::make('all orders', $this->orders->count())
            ->icon('heroicon-o-clipboard-document-list')
            ->description('all the orders related to the user')
            ->descriptionIcon('heroicon-o-clipboard-document-list', IconPosition::Before)
            ->chart([0, 2, 1, 0, 1, 5])
            ->color(Color::Gray);
    }

    private function makeFinishedOrdersStat(): Stat
    {
        return Stat::make('finished orders', $this->orders->where('pivot.status', 'completed')->count())
            ->label('Completed')
            ->icon('heroicon-o-clipboard-document-check')
            ->description('all the orders completed by the user')
            ->descriptionIcon('heroicon-o-clipboard-document-check', IconPosition::Before)
            ->chart([0, 2, 1, 3, 1, 5])
            ->color(Color::Green);
    }

    private function makeRejectedORdersStat(): Stat
    {
        return Stat::make('rejected orders', $this->orders->where('pivot.status', 'rejected')->count())
            ->icon('heroicon-o-archive-box-x-mark')
            ->description('all the orders rejected by the user')
            ->descriptionIcon('heroicon-o-archive-box-x-mark', IconPosition::Before)
            ->chart([5, 1, 3, 1, 2, 0])
            ->color(Color::Red);
    }
}
