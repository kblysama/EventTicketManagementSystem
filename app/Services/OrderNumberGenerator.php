<?php

namespace App\Services;

use App\Models\Order;

class OrderNumberGenerator
{
    public function make(): string
    {
        $year = now()->year;
        $last = Order::query()
            ->where('number', 'like', 'YR-'.$year.'-%')
            ->orderByDesc('id')
            ->value('number');

        $sequence = 1;

        if ($last && preg_match('/YR-\d{4}-(\d+)/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('YR-%d-%05d', $year, $sequence);
    }
}
