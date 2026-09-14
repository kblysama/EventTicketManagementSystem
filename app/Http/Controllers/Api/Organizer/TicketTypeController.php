<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\JsonResponse;

class TicketTypeController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        $this->authorize('manage', $event);

        return response()->json(['data' => $event->ticketTypes]);
    }

    public function store(StoreTicketTypeRequest $request, Event $event): JsonResponse
    {
        $type = $event->ticketTypes()->create($request->validated());

        return response()->json(['data' => $type], 201);
    }

    public function update(StoreTicketTypeRequest $request, Event $event, TicketType $ticketType): JsonResponse
    {
        abort_unless($ticketType->event_id === $event->id, 404);
        $ticketType->update($request->validated());

        return response()->json(['data' => $ticketType]);
    }
}
