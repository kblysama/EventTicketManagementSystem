<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Events\TicketCheckedIn;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckInService
{
    /**
     * @return array{ticket: Ticket, reused: bool, message: string}
     */
    public function verify(Event $event, string $code, User $actor): array
    {
        $ticket = Ticket::query()
            ->where('event_id', $event->id)
            ->where('code', mb_strtoupper(trim($code)))
            ->first();

        if (! $ticket) {
            throw ValidationException::withMessages([
                'code' => 'Bu kod bu etkinlik için geçerli değil.',
            ]);
        }

        if ($ticket->isUsed()) {
            return [
                'ticket' => $ticket,
                'reused' => true,
                'message' => $ticket->code.' daha önce kullanılmış. İkinci denemede bilet uyarı gösterir.',
            ];
        }

        $ticket->update([
            'status' => TicketStatus::Used,
            'checked_in_at' => now(),
            'checked_in_by' => $actor->id,
        ]);

        $ticket->refresh();

        safe_broadcast(new TicketCheckedIn($ticket));

        return [
            'ticket' => $ticket,
            'reused' => false,
            'message' => $ticket->code.' geçerli. Giriş doğrulandı.',
        ];
    }
}
