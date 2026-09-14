<?php

namespace App\Http\Controllers\Organizer;

use App\Events\EventUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\CoverImageService;
use App\Services\EventDeletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::query()
            ->where('organizer_id', auth()->id())
            ->with('ticketTypes')
            ->orderBy('starts_at');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%']);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('organizer.events.index', [
            'events' => $query->get(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('organizer.events.form', [
            'event' => new Event,
            'categories' => EventCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEventRequest $request, CoverImageService $covers): JsonResponse|RedirectResponse
    {
        $event = new Event($request->safe()->except(['cover', 'organizer_id']));
        $event->organizer_id = auth()->id();

        if ($request->hasFile('cover')) {
            $event->cover_path = $covers->store($request->file('cover'));
        }

        $event->save();
        safe_broadcast(new EventUpdated($event));

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('organizer.events.show', $event), 'Etkinlik kaydedildi.')
            : redirect()->route('organizer.events.show', $event);
    }

    public function show(Event $event): View
    {
        $this->authorize('manage', $event);
        $event->load(['ticketTypes', 'organizer', 'eventCategory']);

        return view('organizer.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('organizer.events.form', [
            'event' => $event,
            'categories' => EventCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(StoreEventRequest $request, Event $event, CoverImageService $covers): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $event);

        $event->fill($request->safe()->except(['cover', 'organizer_id']));

        if ($request->hasFile('cover')) {
            $event->cover_path = $covers->replace($event->cover_path, $request->file('cover'));
        }

        $event->save();
        safe_broadcast(new EventUpdated($event));

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('organizer.events.show', $event), 'Etkinlik güncellendi.')
            : redirect()->route('organizer.events.show', $event);
    }

    public function destroy(Event $event, EventDeletionService $deleter): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $event);
        $deleter->delete($event);
        safe_broadcast(new EventUpdated($event));

        return request()->expectsJson() || request()->ajax()
            ? ajax_redirect(route('organizer.events.index'), 'Etkinlik silindi.')
            : redirect()->route('organizer.events.index')->with('status', 'Etkinlik silindi.');
    }
}
