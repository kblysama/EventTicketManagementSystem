<?php

use Illuminate\Support\Carbon;

function money_tr(int $amount): string
{
    return '₺'.number_format($amount, 0, ',', '.');
}

function format_tr_date(Carbon|string|null $date, string $format = 'd F Y'): string
{
    if ($date === null) {
        return '';
    }

    return Carbon::parse($date)->locale('tr')->translatedFormat($format);
}

function format_tr_datetime(Carbon|string|null $date): string
{
    if ($date === null) {
        return '';
    }

    return Carbon::parse($date)->locale('tr')->translatedFormat('d F Y · H:i');
}

function ajax_redirect(string $url, string $message = ''): \Illuminate\Http\JsonResponse
{
    return response()->json(array_filter([
        'redirect' => $url,
        'message' => $message,
    ]));
}

function safe_broadcast(object $event): void
{
    try {
        event($event);
    } catch (Throwable $exception) {
        report($exception);
    }
}
