<?php

namespace App\Enums;

enum TicketSaleStatus: string
{
    case OnSale = 'on_sale';
    case Paused = 'paused';

    public function label(): string
    {
        return match ($this) {
            self::OnSale => 'Satışta',
            self::Paused => 'Durduruldu',
        };
    }
}
