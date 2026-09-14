<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Organizer\CheckInController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboardController;
use App\Http\Controllers\Organizer\EventCategoryController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\TicketTypeController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/etkinlikler/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/giris', [AuthenticatedSessionController::class, 'store']);
    Route::get('/kayit', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/kayit', [RegisteredUserController::class, 'store']);
    Route::get('/sifremi-unuttum', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/sifremi-unuttum', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/sifre-sifirla/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/sifre-sifirla', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/cikis', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/etkinlikler/{event}/satin-al', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/etkinlikler/{event}/satin-al', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/siparislerim', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/siparislerim/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/biletlerim', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/biletlerim/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});

Route::middleware(['auth', 'role:organizer,admin'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', OrganizerDashboardController::class)->name('dashboard');
    Route::get('/etkinlikler', [OrganizerEventController::class, 'index'])->name('events.index');
    Route::get('/etkinlikler/olustur', [OrganizerEventController::class, 'create'])->name('events.create');
    Route::post('/etkinlik-turleri', [EventCategoryController::class, 'store'])->name('categories.store');
    Route::post('/etkinlikler', [OrganizerEventController::class, 'store'])->name('events.store');
    Route::get('/etkinlikler/{event}', [OrganizerEventController::class, 'show'])->name('events.show');
    Route::get('/etkinlikler/{event}/duzenle', [OrganizerEventController::class, 'edit'])->name('events.edit');
    Route::post('/etkinlikler/{event}', [OrganizerEventController::class, 'update'])->name('events.update');
    Route::post('/etkinlikler/{event}/sil', [OrganizerEventController::class, 'destroy'])->name('events.destroy');
    Route::get('/bilet-tipleri', [TicketTypeController::class, 'home'])->name('ticket-types.home');
    Route::get('/etkinlikler/{event}/bilet-tipleri', [TicketTypeController::class, 'index'])->name('ticket-types.index');
    Route::post('/etkinlikler/{event}/bilet-tipleri', [TicketTypeController::class, 'store'])->name('ticket-types.store');
    Route::post('/etkinlikler/{event}/bilet-tipleri/{ticketType}', [TicketTypeController::class, 'update'])->name('ticket-types.update');
    Route::post('/etkinlikler/{event}/bilet-tipleri/{ticketType}/sil', [TicketTypeController::class, 'destroy'])->name('ticket-types.destroy');
    Route::get('/check-in', [CheckInController::class, 'home'])->name('check-in.home');
    Route::get('/etkinlikler/{event}/check-in', [CheckInController::class, 'show'])->name('check-in.show');
    Route::post('/etkinlikler/{event}/check-in', [CheckInController::class, 'store'])->name('check-in.store');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/kullanicilar', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/kullanicilar', [AdminUserController::class, 'store'])->name('users.store');
    Route::post('/kullanicilar/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/kullanicilar/{user}/rol', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::post('/kullanicilar/{user}/sil', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/etkinlikler', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/etkinlikler/olustur', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/etkinlikler', [AdminEventController::class, 'store'])->name('events.store');
    Route::post('/etkinlikler/{event}/sil', [AdminEventController::class, 'destroy'])->name('events.destroy');
    Route::get('/siparisler', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/siparisler/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/satis-raporu', [ReportController::class, 'sales'])->name('reports.sales');
});
