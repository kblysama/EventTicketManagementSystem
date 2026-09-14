@extends('layouts.public')
@section('title', $ticket->code.' · yerin.')
@section('header-intro')
    <div class="eyebrow mb-2">Biletlerim</div>
    <h1 class="display-title mb-2">O akşam, oradasın.</h1>
    <p class="muted mb-0">Girişte aşağıdaki bilet kodunu göster.</p>
@endsection
@section('content')
<div class="container">
    <div class="ticket-pass mx-auto">
        <div class="ticket-pass-top">
            <div class="d-flex justify-content-between mb-4">
                <strong>yerin.</strong>
                <span>{{ mb_strtoupper($ticket->ticketType->name) }}</span>
            </div>
            <div class="cover-title">{{ $ticket->event->title }}</div>
            <p class="mt-3 mb-0">{{ format_tr_datetime($ticket->event->starts_at) }}<br>{{ $ticket->event->venue }}, {{ $ticket->event->city }}</p>
        </div>
        <div class="ticket-pass-bottom">
            <div class="d-flex justify-content-between mb-3">
                <strong>{{ $ticket->order->buyer_name }}</strong>
                <span>{{ $ticket->status->label() }}</span>
            </div>
            <div class="code-box">{{ $ticket->code }}</div>
            <p class="muted small mt-3 mb-0">Sipariş {{ $ticket->order->displayNumber() }}<br>Bu kod tek giriş hakkı sağlar. Başkalarıyla paylaşma.</p>
        </div>
    </div>
</div>
@endsection
