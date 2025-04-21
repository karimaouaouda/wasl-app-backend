<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'source_app',
        'restaurant_data',
        'status',
    ];

    protected $casts = [
        'restaurant_data' => 'array',
        'status' => OrderStatus::class,
    ];


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }



}
