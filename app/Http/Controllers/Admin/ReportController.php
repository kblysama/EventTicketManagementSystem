<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(): View
    {
        $events = Event::query()->with('ticketTypes')->orderBy('starts_at')->get();

        $rows = $events->map(fn (Event $event) => [
            'event' => $event,
            'sold' => $event->soldCount(),
            'revenue' => $event->orders()->sum('total'),
            'remaining' => $event->remainingCount(),
            'checked_in' => $event->checkedInCount(),
        ]);

        return view('admin.reports.sales', [
            'rows' => $rows,
            'totalSales' => $rows->sum('revenue'),
            'soldTickets' => $rows->sum('sold'),
            'activeEvents' => $events->where('status', \App\Enums\EventStatus::Published)->count(),
            'totalCapacity' => $events->sum(fn (Event $event) => $event->capacity()),
            'checkedIn' => $rows->sum('checked_in'),
        ]);
    }
}
