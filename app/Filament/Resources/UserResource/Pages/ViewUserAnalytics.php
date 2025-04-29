<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserAnalytics extends ViewRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable
     */
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return "View User : " . $this->record->getAttribute('name');
    }


    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserStatsView::make([
                'user' => $this->record
            ]),
            UserResource\Widgets\UserOrdersTable::make([
                'user' => $this->record
            ])
        ];
    }
}
