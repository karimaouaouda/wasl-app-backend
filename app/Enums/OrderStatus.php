<?php

namespace App\Enums;

use Illuminate\Contracts\Support\Arrayable;

enum OrderStatus: string implements  Arrayable
{
    case PREPARING = 'preparing'; // restaurant is preparing the order

    case CANCELLED = 'cancelled'; // order canceled from the client

    case READY = 'ready'; // order ready to pickup

    case ACCEPTED = 'accepted';

    case PICKED = 'picked';

    case COMPLETED = 'completed';

    public static function values(): array
    {
        $values = [];
        foreach (OrderStatus::cases() as $case){
            $values[] = $case->value;
        }

        return $values;
    }

    public function toArray(): array
    {
        $values = [];
        foreach (OrderStatus::cases() as $case) {
            $values[$case->value] = $case->name;
        }

        return $values;
    }
}
