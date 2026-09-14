<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Enums\TicketSaleStatus;
use App\Enums\TicketStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_delete_an_event_and_its_related_records(): void
    {
        Storage::fake('public');

        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $buyer = User::factory()->create(['role' => Role::Attendee]);
        $categoryId = EventCategory::query()->where('slug', 'music')->value('id');

        $event = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Silinecek Etkinlik',
            'description' => 'Test',
            'venue' => 'Mekan',
            'city' => 'İstanbul',
            'event_category_id' => $categoryId,
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addWeek()->addHours(2),
            'status' => EventStatus::Published,
            'cover_path' => 'covers/silinecek.webp',
        ]);

        Storage::disk('public')->put('covers/silinecek.webp', 'img');

        $type = TicketType::query()->create([
            'event_id' => $event->id,
            'name' => 'Standart',
            'price' => 100,
            'capacity' => 10,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        $order = Order::query()->create([
            'number' => 'YR-DEL-00001',
            'user_id' => $buyer->id,
            'event_id' => $event->id,
            'ticket_type_id' => $type->id,
            'buyer_name' => $buyer->name,
            'buyer_email' => $buyer->email,
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'status' => OrderStatus::Completed,
        ]);

        $ticket = Ticket::query()->create([
            'order_id' => $order->id,
            'ticket_type_id' => $type->id,
            'event_id' => $event->id,
            'user_id' => $buyer->id,
            'code' => 'YR-DEL-TEST',
            'status' => TicketStatus::Unused,
        ]);

        $this->actingAs($organizer)
            ->postJson(route('organizer.events.destroy', $event))
            ->assertOk()
            ->assertJsonPath('redirect', route('organizer.events.index'));

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseMissing('ticket_types', ['id' => $type->id]);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
        Storage::disk('public')->assertMissing('covers/silinecek.webp');
    }

    public function test_admin_delete_redirects_to_admin_events_index(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $categoryId = EventCategory::query()->where('slug', 'music')->value('id');

        $event = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Admin Silinecek',
            'description' => 'Test',
            'venue' => 'Mekan',
            'city' => 'İstanbul',
            'event_category_id' => $categoryId,
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addWeek()->addHours(2),
            'status' => EventStatus::Published,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.events.destroy', $event))
            ->assertOk()
            ->assertJsonPath('redirect', route('admin.events.index'));

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
