<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketSaleStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCreated;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private OrderNumberGenerator $orderNumbers,
        private TicketCodeGenerator $ticketCodes,
    ) {}

    public function purchase(User $user, Event $event, TicketType $ticketType, int $quantity, string $buyerName, string $buyerEmail): Order
    {
        if ($ticketType->event_id !== $event->id) {
            throw ValidationException::withMessages([
                'ticket_type_id' => 'Bu bilet tipi bu etkinliğe ait değil.',
            ]);
        }

        if ($event->status !== EventStatus::Published) {
            throw ValidationException::withMessages([
                'event' => 'Bu etkinlik henüz satışta değil.',
            ]);
        }

        return DB::transaction(function () use ($user, $event, $ticketType, $quantity, $buyerName, $buyerEmail) {
            $locked = TicketType::query()->whereKey($ticketType->id)->lockForUpdate()->firstOrFail();

            if ($locked->sale_status !== TicketSaleStatus::OnSale) {
                throw ValidationException::withMessages([
                    'ticket_type_id' => 'Bu bilet tipi şu anda satışta değil.',
                ]);
            }

            if ($locked->remainingCount() < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Yeterli bilet kalmadı.',
                ]);
            }

            $order = Order::query()->create([
                'number' => $this->orderNumbers->make(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_type_id' => $locked->id,
                'buyer_name' => $buyerName,
                'buyer_email' => $buyerEmail,
                'quantity' => $quantity,
                'unit_price' => $locked->price,
                'total' => $locked->price * $quantity,
                'status' => OrderStatus::Completed,
            ]);

            for ($i = 0; $i < $quantity; $i++) {
                Ticket::query()->create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $locked->id,
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'code' => $this->ticketCodes->make(),
                    'status' => TicketStatus::Unused,
                ]);
            }

            $order->load(['event', 'ticketType', 'tickets']);

            safe_broadcast(new OrderCreated($order));

            return $order;
        });
    }
}
