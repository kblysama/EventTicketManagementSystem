<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Event;
use App\Models\TicketType;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Event $event): View
    {
        $event->load('ticketTypes');

        abort_unless($event->status->value === 'published', 404);

        return view('checkout.create', compact('event'));
    }

    public function store(CheckoutRequest $request, Event $event, OrderService $orders): JsonResponse|RedirectResponse
    {
        $ticketType = TicketType::query()->findOrFail($request->integer('ticket_type_id'));

        $order = $orders->purchase(
            $request->user(),
            $event,
            $ticketType,
            $request->integer('quantity'),
            $request->string('buyer_name')->toString(),
            $request->string('buyer_email')->toString(),
        );

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('orders.show', $order), 'Yerin hazır.')
            : redirect()->route('orders.show', $order);
    }
}
