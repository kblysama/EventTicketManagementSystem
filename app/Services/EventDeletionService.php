<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventDeletionService
{
    public function delete(Event $event): void
    {
        DB::transaction(function () use ($event): void {
            $event->tickets()->delete();
            $event->orders()->delete();
            $event->ticketTypes()->delete();

            if ($event->cover_path) {
                Storage::disk('public')->delete($event->cover_path);
            }

            $event->delete();
        });
    }
}
