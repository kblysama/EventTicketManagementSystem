<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = $request->user()->tickets()->with(['event', 'ticketType', 'order'])->latest()->get();

        return response()->json([
            'data' => $tickets->map(fn (Ticket $ticket) => $this->payload($ticket)),
        ]);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);
        $ticket->load(['event', 'ticketType', 'order']);

        return response()->json(['data' => $this->payload($ticket)]);
    }

    private function payload(Ticket $ticket): array
    {
        return [
            'code' => $ticket->code,
            'status' => $ticket->status->value,
            'event' => [
                'id' => $ticket->event->id,
                'title' => $ticket->event->title,
                'starts_at' => $ticket->event->starts_at->toIso8601String(),
                'venue' => $ticket->event->venue,
                'city' => $ticket->event->city,
            ],
            'ticket_type' => $ticket->ticketType->name,
            'order_number' => $ticket->order->number,
        ];
    }
}
