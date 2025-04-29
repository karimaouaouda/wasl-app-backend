<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('order information')
                    ->schema([
                        Forms\Components\Wizard::make([
                            Forms\Components\Wizard\Step::make('order information')
                                ->schema([
                                    Forms\Components\TextInput::make('source_app')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('the order source app'),
                                    Forms\Components\Select::make('status')
                                        ->options(OrderStatus::class),
                                ]),
                            Forms\Components\Wizard\Step::make('restaurant information')
                                ->schema([
                                    Forms\Components\Repeater::make('restaurant_data')
                                        ->collapsible()
                                        ->reorderable(false)
                                        ->deletable(false)
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->label('restaurant name')
                                                ->required()
                                                ->minLength(5)
                                                ->maxLength(50),
                                            Forms\Components\TextInput::make('logo_url')
                                                ->required()
                                                ->url(),
                                            TextInput::make('phone')
                                                ->tel()
                                                ->required(),
                                            TextInput::make('whatsapp')
                                                ->nullable()
                                                ->tel(),
                                            Forms\Components\Textarea::make('description')
                                                ->nullable()
                                                ->minLength(10)
                                                ->maxLength(255)
                                        ])->maxItems(1)->minItems(1),
                                ]),
                            Forms\Components\Wizard\Step::make('client information')
                                ->schema([
                                    Forms\Components\Repeater::make('client_data')
                                        ->reorderable(false)
                                        ->deletable(false)
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->label('client name')
                                                ->nullable()
                                                ->minLength(5)
                                                ->maxLength(50),
                                            TextInput::make('phone')
                                                ->tel()
                                                ->required(),
                                            TextInput::make('whatsapp')
                                                ->nullable()
                                                ->tel(),
                                        ])->maxItems(1)->minItems(1)->collapsible()
                                ]),
                            Forms\Components\Wizard\Step::make('order items')
                                ->schema([
                                    Forms\Components\Repeater::make('items')
                                        ->relationship('items')
                                        ->schema([
                                            Forms\Components\TextInput::make('quantity')
                                                ->maxValue(100)
                                                ->minValue(1),
                                            Forms\Components\TextInput::make('item_name')
                                                ->required()
                                                ->maxLength(255)
                                                ->minLength(10),
                                            Forms\Components\TextInput::make('price')
                                                ->numeric()
                                                ->minValue(0.0)
                                                ->required(),
                                            Forms\Components\Textarea::make('extra_description')
                                                ->nullable()
                                                ->minLength(10)
                                                ->maxLength(255)

                                        ])->minItems(1)->maxItems(5)
                                ])
                        ]),


                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->badge()
                    ->prefix('#'),
                TextColumn::make('source_app')
                    ->badge(),
                TextColumn::make('restaurant_data.name')
                    ->badge(),
                Tables\Columns\SelectColumn::make('status')
                    ->options(OrderStatus::class),
                TextColumn::make('items_count')
                    ->default(0)
                    ->formatStateUsing(fn($record) => $record->items()->count())
                    ->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
