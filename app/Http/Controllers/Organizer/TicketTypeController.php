<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function home(): RedirectResponse|View
    {
        $event = Event::query()->manageableBy(auth()->user())->first();

        return $event
            ? redirect()->route('organizer.ticket-types.index', $event)
            : view('organizer.empty', [
                'current' => 'tickets',
                'eyebrow' => 'Bilet yönetimi',
                'heading' => 'Önce bir etkinlik lazım.',
                'copy' => 'Bilet tipi eklemek için bir etkinlik oluştur.',
            ]);
    }

    public function index(Event $event): View
    {
        $this->authorize('manage', $event);
        $event->load('ticketTypes');

        return view('organizer.ticket-types.index', [
            'event' => $event,
            'events' => Event::query()->manageableBy(auth()->user())->get(),
        ]);
    }

    public function store(StoreTicketTypeRequest $request, Event $event): JsonResponse|RedirectResponse
    {
        $event->ticketTypes()->create($request->validated());

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('organizer.ticket-types.index', $event), 'Bilet tipi eklendi.')
            : redirect()->route('organizer.ticket-types.index', $event);
    }

    public function update(StoreTicketTypeRequest $request, Event $event, TicketType $ticketType): JsonResponse|RedirectResponse
    {
        abort_unless($ticketType->event_id === $event->id, 404);

        $ticketType->update($request->validated());

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('organizer.ticket-types.index', $event), 'Bilet tipi güncellendi.')
            : redirect()->route('organizer.ticket-types.index', $event);
    }

    public function destroy(Event $event, TicketType $ticketType): JsonResponse|RedirectResponse
    {
        $this->authorize('manage', $event);
        abort_unless($ticketType->event_id === $event->id, 404);

        if (! $ticketType->canBeDeleted()) {
            return request()->expectsJson() || request()->ajax()
                ? response()->json(['message' => 'Satılmış biletleri olan bir bilet tipi silinemez.'], 422)
                : back()->withErrors(['ticket_type' => 'Satılmış biletleri olan bir bilet tipi silinemez.']);
        }

        $ticketType->delete();

        return request()->expectsJson() || request()->ajax()
            ? ajax_redirect(route('organizer.ticket-types.index', $event), 'Bilet tipi silindi.')
            : redirect()->route('organizer.ticket-types.index', $event);
    }
}
