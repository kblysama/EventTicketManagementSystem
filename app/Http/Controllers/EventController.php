<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::query()
            ->published()
            ->with(['ticketTypes', 'eventCategory'])
            ->orderBy('starts_at');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%'])
                    ->orWhereRaw('LOWER(venue) LIKE ?', ['%'.mb_strtolower($search).'%'])
                    ->orWhereRaw('LOWER(city) LIKE ?', ['%'.mb_strtolower($search).'%']);
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('eventCategory', fn ($builder) => $builder->where('slug', $category));
        }

        $events = $query->get();
        $featured = Event::query()->published()->with(['ticketTypes', 'eventCategory'])->orderBy('starts_at')->first();

        return view('events.index', [
            'events' => $events,
            'featured' => $featured,
            'categories' => EventCategory::query()->orderBy('name')->get(),
            'activeCategory' => $request->query('category'),
            'search' => $search,
        ]);
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load(['ticketTypes', 'organizer', 'eventCategory']);

        if ($event->status !== EventStatus::Published && ! auth()->user()?->can('update', $event)) {
            abort(404);
        }

        return view('events.show', compact('event'));
    }
}
