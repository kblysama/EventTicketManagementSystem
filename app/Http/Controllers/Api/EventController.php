<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::query()->published()->with(['ticketTypes', 'eventCategory'])->orderBy('starts_at');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%'])
                    ->orWhereRaw('LOWER(venue) LIKE ?', ['%'.mb_strtolower($search).'%']);
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('eventCategory', fn ($builder) => $builder->where('slug', $category));
        }

        return response()->json([
            'data' => $query->get()->map(fn (Event $event) => $this->payload($event)),
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        $this->authorize('view', $event);
        $event->load(['ticketTypes', 'organizer', 'eventCategory']);

        return response()->json(['data' => $this->payload($event, true)]);
    }

    private function payload(Event $event, bool $detailed = false): array
    {
        $data = [
            'id' => $event->id,
            'slug' => $event->slug,
            'title' => $event->title,
            'description' => $event->description,
            'venue' => $event->venue,
            'city' => $event->city,
            'category' => $event->eventCategory?->slug,
            'category_label' => $event->eventCategory?->name,
            'starts_at' => $event->starts_at->toIso8601String(),
            'ends_at' => $event->ends_at->toIso8601String(),
            'status' => $event->status->value,
            'cover_url' => $event->coverUrl(),
            'starting_price' => $event->startingPrice(),
            'sold' => $event->soldCount(),
            'capacity' => $event->capacity(),
            'ticket_types' => $event->ticketTypes->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'description' => $type->description,
                'price' => $type->price,
                'capacity' => $type->capacity,
                'remaining' => $type->remainingCount(),
                'sale_status' => $type->sale_status->value,
            ]),
        ];

        if ($detailed) {
            $data['organizer'] = [
                'id' => $event->organizer?->id,
                'name' => $event->organizer?->name,
            ];
        }

        return $data;
    }
}
