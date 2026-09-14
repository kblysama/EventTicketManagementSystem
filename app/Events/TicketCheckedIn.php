<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCheckedIn implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->ticket->user_id),
            new PrivateChannel('organizer.'.$this->ticket->event->organizer_id),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.checked-in';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'code' => $this->ticket->code,
            'event_id' => $this->ticket->event_id,
            'status' => $this->ticket->status->value,
        ];
    }
}
