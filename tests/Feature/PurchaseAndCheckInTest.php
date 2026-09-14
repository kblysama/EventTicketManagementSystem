<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\Role;
use App\Enums\TicketSaleStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseAndCheckInTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendee_can_purchase_and_organizer_can_check_in(): void
    {
        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $attendee = User::factory()->create(['role' => Role::Attendee]);

        $event = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Test Caz',
            'description' => 'Test',
            'venue' => 'Test Mekan',
            'city' => 'İstanbul',
            'event_category_id' => EventCategory::query()->where('slug', 'music')->value('id'),
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addWeek()->addHours(3),
            'status' => EventStatus::Published,
        ]);

        $type = TicketType::query()->create([
            'event_id' => $event->id,
            'name' => 'Standart',
            'price' => 750,
            'capacity' => 10,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        $this->actingAs($attendee)
            ->postJson(route('checkout.store', $event), [
                'ticket_type_id' => $type->id,
                'quantity' => 1,
                'buyer_name' => $attendee->name,
                'buyer_email' => $attendee->email,
            ])
            ->assertOk()
            ->assertJsonStructure(['redirect']);

        $ticket = Ticket::query()->first();
        $this->assertNotNull($ticket);

        $this->actingAs($organizer)
            ->postJson(route('organizer.check-in.store', $event), [
                'code' => $ticket->code,
            ])
            ->assertOk()
            ->assertJson(['reused' => false]);

        $this->actingAs($organizer)
            ->postJson(route('organizer.check-in.store', $event), [
                'code' => $ticket->code,
            ])
            ->assertStatus(409)
            ->assertJson(['reused' => true]);
    }
}
