<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UserOrdersTable extends BaseWidget
{
    public User $user;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function(){
                    return Order::query()
                        ->join('user_orders', 'user_orders.order_id', '=', 'orders.id')
                        ->where('user_orders.user_id', $this->user->id);

                }
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->badge()
                    ->label('delivery id')
                    ->prefix('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('order action date')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('order action')
                    ->badge()
            ]);
    }
}
