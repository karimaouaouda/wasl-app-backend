<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PREPARING = 'preparing'; // restaurant is preparing the order

    case FINISHED = 'finished'; // order arrived to client

    case CANCELLED = 'cancelled'; // order canceled from the client

    case READY = 'ready'; // order ready to pickup

    public static function values(): array
    {
        $values = [];
        foreach (OrderStatus::cases() as $case) {
            $values[] = $case->value;
        }

        return $values;
    }
}
