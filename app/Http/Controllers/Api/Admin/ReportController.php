<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function sales(): JsonResponse
    {
        $events = Event::query()->with('ticketTypes')->orderBy('starts_at')->get();

        return response()->json([
            'data' => [
                'total_sales' => Order::query()->sum('total'),
                'sold_tickets' => $events->sum(fn (Event $event) => $event->soldCount()),
                'users' => User::query()->count(),
                'events' => $events->map(fn (Event $event) => [
                    'title' => $event->title,
                    'sold' => $event->soldCount(),
                    'revenue' => $event->orders()->sum('total'),
                    'remaining' => $event->remainingCount(),
                    'checked_in' => $event->checkedInCount(),
                ]),
            ],
        ]);
    }
}
