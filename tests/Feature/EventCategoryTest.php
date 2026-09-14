<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_add_an_event_category(): void
    {
        $organizer = User::factory()->create(['role' => Role::Organizer]);

        $this->actingAs($organizer)
            ->postJson(route('organizer.categories.store'), ['name' => 'Konferans'])
            ->assertCreated()
            ->assertJsonPath('category.name', 'Konferans');

        $this->assertDatabaseHas('event_categories', ['name' => 'Konferans']);
    }

    public function test_existing_category_name_is_reused(): void
    {
        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $existing = EventCategory::query()->where('slug', 'music')->first();

        $this->actingAs($organizer)
            ->postJson(route('organizer.categories.store'), ['name' => $existing->name])
            ->assertOk()
            ->assertJsonPath('category.id', $existing->id);

        $this->assertSame(1, EventCategory::query()->where('name', $existing->name)->count());
    }
}
