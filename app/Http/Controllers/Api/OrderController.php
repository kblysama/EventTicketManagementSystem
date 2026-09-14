<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()->with(['event', 'ticketType', 'tickets'])->latest()->get();

        return response()->json([
            'data' => $orders->map(fn (Order $order) => $this->payload($order)),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $order->load(['event', 'ticketType', 'tickets']);

        return response()->json(['data' => $this->payload($order)]);
    }

    public function store(CheckoutRequest $request, OrderService $orders): JsonResponse
    {
        $ticketType = TicketType::query()->findOrFail($request->integer('ticket_type_id'));
        $event = Event::query()->findOrFail($ticketType->event_id);

        $order = $orders->purchase(
            $request->user(),
            $event,
            $ticketType,
            $request->integer('quantity'),
            $request->string('buyer_name')->toString(),
            $request->string('buyer_email')->toString(),
        );

        return response()->json(['data' => $this->payload($order)], 201);
    }

    private function payload(Order $order): array
    {
        return [
            'id' => $order->id,
            'number' => $order->number,
            'buyer_name' => $order->buyer_name,
            'buyer_email' => $order->buyer_email,
            'quantity' => $order->quantity,
            'unit_price' => $order->unit_price,
            'total' => $order->total,
            'status' => $order->status->value,
            'created_at' => $order->created_at?->toIso8601String(),
            'event' => [
                'id' => $order->event->id,
                'title' => $order->event->title,
                'slug' => $order->event->slug,
            ],
            'ticket_type' => [
                'id' => $order->ticketType->id,
                'name' => $order->ticketType->name,
            ],
            'tickets' => $order->tickets->map(fn ($ticket) => [
                'code' => $ticket->code,
                'status' => $ticket->status->value,
            ]),
        ];
    }
}
