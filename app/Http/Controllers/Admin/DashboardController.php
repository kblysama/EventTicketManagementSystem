<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $events = Event::query()->with('ticketTypes')->orderBy('starts_at')->get();

        return view('admin.dashboard', [
            'events' => $events,
            'totalSales' => $events->sum(fn (Event $event) => $event->orders()->sum('total')),
            'soldTickets' => $events->sum(fn (Event $event) => $event->soldCount()),
            'activeEvents' => $events->where('status', EventStatus::Published)->count(),
            'totalCapacity' => $events->sum(fn (Event $event) => $event->capacity()),
            'usersCount' => User::query()->count(),
            'nextEvent' => $events->firstWhere('status', EventStatus::Published),
            'chart' => $events->map(fn (Event $event) => [
                'label' => $event->title,
                'sold' => $event->soldCount(),
            ]),
        ]);
    }
}
