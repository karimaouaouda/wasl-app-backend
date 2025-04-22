<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case PICKED = 'picked';

    case ACCEPTED = 'accepted';

    case REJECTED = 'rejected';

    case COMPLETED = 'completed';


    public static function values(): array
    {
        $values = [];
        foreach (DeliveryStatus::cases() as $case){
            $values[] = $case->value;
        }

        return $values;
    }

    public function toArray(): array
    {
        $values = [];
        foreach (DeliveryStatus::cases() as $case) {
            $values[$case->value] = $case->name;
        }

        return $values;
    }
}
