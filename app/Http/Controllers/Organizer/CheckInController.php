<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Models\Event;
use App\Services\CheckInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function home(): RedirectResponse|View
    {
        $event = Event::query()->manageableBy(auth()->user())->first();

        return $event
            ? redirect()->route('organizer.check-in.show', $event)
            : view('organizer.empty', [
                'current' => 'checkin',
                'eyebrow' => 'Giriş doğrulama',
                'heading' => 'Önce bir etkinlik lazım.',
                'copy' => 'Check-in yapmak için bir etkinlik oluştur.',
            ]);
    }

    public function show(Event $event): View
    {
        $this->authorize('manage', $event);

        return view('organizer.check-in.show', [
            'event' => $event,
            'events' => Event::query()->manageableBy(auth()->user())->get(),
        ]);
    }

    public function store(CheckInRequest $request, Event $event, CheckInService $checkIn): JsonResponse
    {
        $result = $checkIn->verify($event, $request->string('code')->toString(), $request->user());

        return response()->json([
            'reused' => $result['reused'],
            'message' => $result['message'],
            'ticket' => [
                'code' => $result['ticket']->code,
                'status' => $result['ticket']->status->label(),
            ],
        ], $result['reused'] ? 409 : 200);
    }
}
