<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $events = Event::query()
            ->where('organizer_id', auth()->id())
            ->with('ticketTypes')
            ->orderBy('starts_at')
            ->get();

        $chart = $events->map(fn (Event $event) => [
            'label' => $event->title,
            'sold' => $event->soldCount(),
        ]);

        return view('organizer.dashboard', [
            'events' => $events,
            'totalSales' => $events->sum(fn (Event $event) => $event->orders()->sum('total')),
            'soldTickets' => $events->sum(fn (Event $event) => $event->soldCount()),
            'activeEvents' => $events->where('status', EventStatus::Published)->count(),
            'totalCapacity' => $events->sum(fn (Event $event) => $event->capacity()),
            'checkedIn' => $events->sum(fn (Event $event) => $event->checkedInCount()),
            'nextEvent' => $events->firstWhere('status', EventStatus::Published),
            'chart' => $chart,
        ]);
    }
}
