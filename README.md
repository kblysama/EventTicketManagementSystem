# yerin. — Event Ticket Management System

Laravel 12+ (iskelet Laravel 13) + Blade + Bootstrap 5 + PostgreSQL ile geliştirilmiş etkinlik ve biletleme web uygulaması. Mobil uygulama aynı REST API ve Reverb kanalını kullanır.

## Teknolojiler

- PHP 8.3, Laravel, Blade, PostgreSQL
- Docker Compose (app + PostgreSQL + Redis + Reverb)
- Bootstrap 5, Manrope, Chart.js
- Javascript Ajax Form Request
- Laravel Sanctum, Laravel Reverb
- Enum, Form Request, Policy, Middleware, Route groups

## Çalıştırma

```bash
cp .env.example .env
docker compose up --build
```

İlk açılışta container `composer install`, `key:generate`, `migrate --seed` ve `storage:link` çalıştırır.

Uygulama: [http://localhost:8000](http://localhost:8000)  
WebSocket: `ws://localhost:8080`

### Docker olmadan

PHP 8.3, Composer, PostgreSQL ve Node 22 gerekir.

```bash
composer install
copy .env.example .env
php artisan key:generate
# .env içinde DB_HOST=127.0.0.1 yap
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Demo hesaplar

Şifrelerin hepsi `password`.

| Rol | E-posta |
|---|---|
| Admin | ada@example.com |
| Organizatör | deniz@example.com |
| Attendee | ece@example.com |

## API

Base: `/api`

```http
POST /api/auth/register
POST /api/auth/login
GET  /api/me
GET  /api/events
GET  /api/events/{slug}
POST /api/orders
GET  /api/orders
GET  /api/tickets
POST /api/organizer/events
POST /api/organizer/events/{event}/check-in
GET  /api/admin/reports/sales
```

Giriş sonrası `Authorization: Bearer {token}` kullanın.

Realtime kanallar: `events`, `private-user.{id}`, `private-organizer.{id}`, `private-admin`. Event adları: `order.created`, `event.updated`, `ticket.checked-in`.

## Tasarım

Renkler: `#C9FBFF` `#C2FCF7` `#85BDBF` `#57737A` `#040F0F`  
Font: Manrope  
Referans ekranlar: `yerin-web-ui/`
