<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Events\EventUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
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
        $query = Event::query()->with(['ticketTypes', 'organizer'])->orderBy('starts_at');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%']);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.events.index', [
            'events' => $query->get(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('organizer.events.form', [
            'event' => new Event,
            'organizers' => User::query()->where('role', Role::Organizer)->orderBy('name')->get(),
            'categories' => EventCategory::query()->orderBy('name')->get(),
            'adminForm' => true,
        ]);
    }

    public function store(StoreEventRequest $request, CoverImageService $covers): JsonResponse|RedirectResponse
    {
        $event = new Event($request->safe()->except(['cover', 'organizer_id']));
        $event->organizer_id = $request->integer('organizer_id') ?: User::query()->where('role', Role::Organizer)->value('id') ?: $request->user()->id;

        if ($request->hasFile('cover')) {
            $event->cover_path = $covers->store($request->file('cover'));
        }

        $event->save();
        safe_broadcast(new EventUpdated($event));

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('admin.events.index'), 'Etkinlik kaydedildi.')
            : redirect()->route('admin.events.index');
    }

    public function destroy(Event $event, EventDeletionService $deleter): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $event);
        $deleter->delete($event);
        safe_broadcast(new EventUpdated($event));

        return request()->expectsJson() || request()->ajax()
            ? ajax_redirect(route('admin.events.index'), 'Etkinlik silindi.')
            : redirect()->route('admin.events.index')->with('status', 'Etkinlik silindi.');
    }
}
