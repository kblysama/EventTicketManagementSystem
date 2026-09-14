<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Events\EventUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Services\CoverImageService;
use App\Services\EventDeletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::query()
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('organizer_id', $request->user()->id))
            ->with('ticketTypes')
            ->orderBy('starts_at')
            ->get();

        return response()->json(['data' => $events]);
    }

    public function store(StoreEventRequest $request, CoverImageService $covers): JsonResponse
    {
        $event = new Event($request->safe()->except(['cover', 'organizer_id']));
        $event->organizer_id = $request->user()->isAdmin()
            ? ($request->integer('organizer_id') ?: $request->user()->id)
            : $request->user()->id;

        if ($request->hasFile('cover')) {
            $event->cover_path = $covers->store($request->file('cover'));
        }

        $event->save();
        safe_broadcast(new EventUpdated($event));

        return response()->json(['data' => $event], 201);
    }

    public function update(StoreEventRequest $request, Event $event, CoverImageService $covers): JsonResponse
    {
        $this->authorize('update', $event);
        $event->fill($request->safe()->except(['cover', 'organizer_id']));

        if ($request->hasFile('cover')) {
            $event->cover_path = $covers->replace($event->cover_path, $request->file('cover'));
        }

        $event->save();
        safe_broadcast(new EventUpdated($event));

        return response()->json(['data' => $event]);
    }

    public function destroy(Event $event, EventDeletionService $deleter): JsonResponse
    {
        $this->authorize('delete', $event);
        $deleter->delete($event);
        safe_broadcast(new EventUpdated($event));

        return response()->json(['message' => 'Etkinlik silindi.']);
    }
}
