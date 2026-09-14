<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Event $event) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('events'),
            new PrivateChannel('organizer.'.$this->event->organizer_id),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'event.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->event->id,
            'slug' => $this->event->slug,
            'title' => $this->event->title,
            'status' => $this->event->status->value,
        ];
    }
}
