<?php

namespace Database\Seeders;

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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'ada@example.com'],
            ['name' => 'Ada Kaya', 'password' => Hash::make('password'), 'role' => Role::Admin],
        );

        $organizer = User::query()->updateOrCreate(
            ['email' => 'deniz@example.com'],
            ['name' => 'Deniz Yılmaz', 'password' => Hash::make('password'), 'role' => Role::Organizer],
        );

        $attendee = User::query()->updateOrCreate(
            ['email' => 'ece@example.com'],
            ['name' => 'Ece Demir', 'password' => Hash::make('password'), 'role' => Role::Attendee],
        );

        $guest = User::query()->updateOrCreate(
            ['email' => 'misafir@example.com'],
            ['name' => 'Yerin Misafir', 'password' => Hash::make('password'), 'role' => Role::Attendee],
        );

        if (Event::query()->exists()) {
            return;
        }

        $music = EventCategory::query()->where('slug', 'music')->firstOrFail();
        $theater = EventCategory::query()->where('slug', 'theater')->firstOrFail();
        $workshop = EventCategory::query()->where('slug', 'workshop')->firstOrFail();

        $caz = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Kıyıda Caz',
            'slug' => 'kiyida-caz',
            'description' => "Boğaz kıyısında, cazın özgür ritimleriyle buluşuyoruz. Canlı performanslar ve şehrin ışıkları eşliğinde gündelik telaşa küçük bir ara.\n\nGitmeden önce\nKapılar 19:00'da açılır. Etkinlik 20:00'da başlar. Girişte Yerin bilet kodunu göstermen yeterli.",
            'venue' => 'Beykoz Kundura',
            'city' => 'İstanbul',
            'event_category_id' => $music->id,
            'starts_at' => '2026-10-24 20:00:00',
            'ends_at' => '2026-10-24 23:00:00',
            'status' => EventStatus::Published,
        ]);

        $sahne = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Bir Başka Sahne',
            'slug' => 'bir-baska-sahne',
            'description' => 'Moda sahnesinde yeni bir hikâye. Işık, metin ve seyirci aynı odada.',
            'venue' => 'Moda Sahnesi',
            'city' => 'İstanbul',
            'event_category_id' => $theater->id,
            'starts_at' => '2026-11-07 20:00:00',
            'ends_at' => '2026-11-07 22:30:00',
            'status' => EventStatus::Published,
        ]);

        $urete = Event::query()->create([
            'organizer_id' => $organizer->id,
            'title' => 'Birlikte Üret',
            'slug' => 'birlikte-uret',
            'description' => 'Tasarım atölyesinde eller konuşur. Küçük grup, büyük paylaşım.',
            'venue' => 'Tasarım Atölyesi',
            'city' => 'İstanbul',
            'event_category_id' => $workshop->id,
            'starts_at' => '2026-11-15 14:00:00',
            'ends_at' => '2026-11-15 18:00:00',
            'status' => EventStatus::Published,
        ]);

        $cazStandard = TicketType::query()->create([
            'event_id' => $caz->id,
            'name' => 'Standart',
            'description' => 'Genel giriş',
            'price' => 750,
            'capacity' => 180,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        TicketType::query()->create([
            'event_id' => $caz->id,
            'name' => 'VIP',
            'description' => 'Ön alan erişimi',
            'price' => 1250,
            'capacity' => 20,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        $sahneStandard = TicketType::query()->create([
            'event_id' => $sahne->id,
            'name' => 'Standart',
            'description' => 'Genel giriş',
            'price' => 450,
            'capacity' => 100,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        $ureteStandard = TicketType::query()->create([
            'event_id' => $urete->id,
            'name' => 'Standart',
            'description' => 'Atölye katılımı',
            'price' => 600,
            'capacity' => 30,
            'sale_status' => TicketSaleStatus::OnSale,
        ]);

        $this->seedBulkOrder($guest, $caz, $cazStandard, 126, 'YR-2026-00001');
        $this->seedBulkOrder($guest, $sahne, $sahneStandard, 64, 'YR-2026-00002');
        $this->seedBulkOrder($guest, $urete, $ureteStandard, 18, 'YR-2026-00003');

        $eceOrder = Order::query()->create([
            'number' => 'YR-2026-01422',
            'user_id' => $attendee->id,
            'event_id' => $caz->id,
            'ticket_type_id' => $cazStandard->id,
            'buyer_name' => 'Ece Demir',
            'buyer_email' => 'ece@example.com',
            'quantity' => 2,
            'unit_price' => 750,
            'total' => 1500,
            'status' => OrderStatus::Completed,
            'created_at' => '2026-09-13 14:32:00',
            'updated_at' => '2026-09-13 14:32:00',
        ]);

        Ticket::query()->create([
            'order_id' => $eceOrder->id,
            'ticket_type_id' => $cazStandard->id,
            'event_id' => $caz->id,
            'user_id' => $attendee->id,
            'code' => 'YR-KC-8F2A',
            'status' => TicketStatus::Unused,
        ]);

        Ticket::query()->create([
            'order_id' => $eceOrder->id,
            'ticket_type_id' => $cazStandard->id,
            'event_id' => $caz->id,
            'user_id' => $attendee->id,
            'code' => 'YR-KC-9B3D',
            'status' => TicketStatus::Unused,
        ]);

        unset($admin);
    }

    private function seedBulkOrder(User $buyer, Event $event, TicketType $type, int $quantity, string $number): void
    {
        $order = Order::query()->create([
            'number' => $number,
            'user_id' => $buyer->id,
            'event_id' => $event->id,
            'ticket_type_id' => $type->id,
            'buyer_name' => $buyer->name,
            'buyer_email' => $buyer->email,
            'quantity' => $quantity,
            'unit_price' => $type->price,
            'total' => $type->price * $quantity,
            'status' => OrderStatus::Completed,
        ]);

        $rows = [];
        $now = now();

        for ($i = 1; $i <= $quantity; $i++) {
            $rows[] = [
                'order_id' => $order->id,
                'ticket_type_id' => $type->id,
                'event_id' => $event->id,
                'user_id' => $buyer->id,
                'code' => sprintf('YR-SD-%s%03d', $event->id, $i),
                'status' => TicketStatus::Unused->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Ticket::query()->insert($rows);
    }
}
