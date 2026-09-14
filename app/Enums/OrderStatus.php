<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Tamamlandı',
        };
    }
}
