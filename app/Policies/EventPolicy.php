<?php

namespace App\Policies;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(?User $user, Event $event): bool
    {
        if ($event->status === EventStatus::Published) {
            return true;
        }

        return $user?->isAdmin() || $user?->id === $event->organizer_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOrganizer();
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin() || $user->id === $event->organizer_id;
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function manage(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
