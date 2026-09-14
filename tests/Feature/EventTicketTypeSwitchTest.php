<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\Role;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTicketTypeSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_open_ticket_types_for_a_chosen_event(): void
    {
        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $categoryId = EventCategory::query()->where('slug', 'music')->value('id');

        $first = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'İlk Etkinlik',
            'description' => 'Test',
            'venue' => 'Mekan',
            'city' => 'İstanbul',
            'event_category_id' => $categoryId,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(2),
            'status' => EventStatus::Published,
        ]);

        $second = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'İkinci Etkinlik',
            'description' => 'Test',
            'venue' => 'Mekan',
            'city' => 'İstanbul',
            'event_category_id' => $categoryId,
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addHours(2),
            'status' => EventStatus::Published,
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.ticket-types.home'))
            ->assertRedirect(route('organizer.ticket-types.index', $first));

        $this->withoutVite();

        $this->actingAs($organizer)
            ->get(route('organizer.ticket-types.index', $second))
            ->assertOk()
            ->assertSee('İkinci Etkinlik')
            ->assertSee('İlk Etkinlik')
            ->assertSee(route('organizer.ticket-types.index', $first), false);
    }
}
