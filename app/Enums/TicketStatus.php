<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Unused = 'unused';
    case Used = 'used';

    public function label(): string
    {
        return match ($this) {
            self::Unused => 'Kullanılmadı',
            self::Used => 'Kullanıldı',
        };
    }
}
