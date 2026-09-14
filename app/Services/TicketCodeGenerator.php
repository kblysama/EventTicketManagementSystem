<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketCodeGenerator
{
    public function make(): string
    {
        do {
            $code = 'YR-'.Str::upper(Str::random(2)).'-'.Str::upper(Str::random(4));
        } while (Ticket::query()->where('code', $code)->exists());

        return $code;
    }
}
