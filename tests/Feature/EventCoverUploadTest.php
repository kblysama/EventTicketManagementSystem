<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\Role;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventCoverUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_upload_a_cover_image(): void
    {
        Storage::fake('public');

        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $category = EventCategory::query()->where('slug', 'music')->firstOrFail();

        $this->actingAs($organizer)
            ->postJson(route('organizer.events.store'), [
                'title' => 'Kapak Test',
                'description' => 'Açıklama',
                'venue' => 'Mekan',
                'city' => 'İstanbul',
                'event_category_id' => $category->id,
                'starts_at' => now()->addDay()->toDateTimeString(),
                'ends_at' => now()->addDay()->addHours(2)->toDateTimeString(),
                'status' => EventStatus::Draft->value,
                'cover' => UploadedFile::fake()->image('afis.jpg', 800, 600),
            ])
            ->assertOk();

        $event = Event::query()->where('title', 'Kapak Test')->first();

        $this->assertNotNull($event?->cover_path);
        Storage::disk('public')->assertExists($event->cover_path);
    }
}
