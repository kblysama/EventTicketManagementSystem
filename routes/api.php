<?php

use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Organizer\CheckInController;
use App\Http\Controllers\Api\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Api\Organizer\TicketTypeController;
use App\Http\Controllers\Api\TicketController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

    Route::middleware('role:organizer,admin')->prefix('organizer')->group(function () {
        Route::get('/events', [OrganizerEventController::class, 'index']);
        Route::post('/events', [OrganizerEventController::class, 'store']);
        Route::put('/events/{event}', [OrganizerEventController::class, 'update']);
        Route::delete('/events/{event}', [OrganizerEventController::class, 'destroy']);
        Route::get('/events/{event}/ticket-types', [TicketTypeController::class, 'index']);
        Route::post('/events/{event}/ticket-types', [TicketTypeController::class, 'store']);
        Route::put('/events/{event}/ticket-types/{ticketType}', [TicketTypeController::class, 'update']);
        Route::post('/events/{event}/check-in', [CheckInController::class, 'store']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', fn () => response()->json(['data' => User::query()->latest()->get()]));
        Route::get('/reports/sales', [ReportController::class, 'sales']);
    });
});
