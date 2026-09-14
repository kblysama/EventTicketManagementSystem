<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\Role;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_role_and_delete_users(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $attendee = User::factory()->create([
            'name' => 'Eski Ad',
            'email' => 'eski@example.com',
            'role' => Role::Attendee,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.store'), [
                'name' => 'Yeni Kullanıcı',
                'email' => 'yeni@example.com',
                'password' => 'password123',
                'role' => Role::Organizer->value,
            ])
            ->assertOk()
            ->assertJsonPath('redirect', route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'yeni@example.com',
            'role' => Role::Organizer->value,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.update', $attendee), [
                'name' => 'Yeni Ad',
                'email' => 'yeni-ad@example.com',
                'password' => '',
                'role' => Role::Organizer->value,
            ])
            ->assertOk();

        $attendee->refresh();
        $this->assertSame('Yeni Ad', $attendee->name);
        $this->assertSame('yeni-ad@example.com', $attendee->email);
        $this->assertSame(Role::Organizer, $attendee->role);

        $this->actingAs($admin)
            ->postJson(route('admin.users.role', $attendee), [
                'role' => Role::Attendee->value,
            ])
            ->assertOk();

        $this->assertSame(Role::Attendee, $attendee->fresh()->role);

        $this->actingAs($admin)
            ->postJson(route('admin.users.destroy', $attendee))
            ->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $attendee->id]);
    }

    public function test_admin_cannot_delete_self_or_last_admin_or_organizer_with_events(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $organizer = User::factory()->create(['role' => Role::Organizer]);
        $categoryId = EventCategory::query()->where('slug', 'music')->value('id')
            ?? EventCategory::query()->value('id');

        Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Korunan Etkinlik',
            'description' => 'Test',
            'venue' => 'Mekan',
            'city' => 'İstanbul',
            'event_category_id' => $categoryId,
            'starts_at' => now()->addWeek(),
            'ends_at' => now()->addWeek()->addHours(2),
            'status' => EventStatus::Published,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.destroy', $admin))
            ->assertStatus(422);

        $this->actingAs($admin)
            ->postJson(route('admin.users.role', $admin), [
                'role' => Role::Attendee->value,
            ])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->postJson(route('admin.users.destroy', $organizer))
            ->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $organizer->id]);
    }
}
