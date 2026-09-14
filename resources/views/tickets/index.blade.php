@extends('layouts.public')
@section('title', 'Biletlerim · yerin.')
@section('header-intro')
    <div class="eyebrow mb-2">Hesabım</div>
    <h1 class="display-title mb-2">Biletlerim</h1>
    <p class="muted mb-0">Takviminde güzel bir şey var.</p>
@endsection
@section('content')
<div class="container">
    <div class="row g-4">
        @foreach($tickets as $ticket)
            <div class="col-md-6">
                <div class="event-card">
                    <x-event-cover :event="$ticket->event" />
                    <div class="event-card-body">
                        <span class="status-pill mb-3">{{ $ticket->status->label() }}</span>
                        <h3 class="fw-bold mt-3 mb-1">{{ $ticket->event->title }}</h3>
                        <div class="muted">{{ format_tr_datetime($ticket->event->starts_at) }}</div>
                        <div class="muted mb-3">{{ $ticket->ticketType->name }} · Bilet {{ $loop->iteration }} / {{ $tickets->where('order_id', $ticket->order_id)->count() }}</div>
                        <div class="d-flex justify-content-between">
                            <strong>{{ $ticket->code }}</strong>
                            <a href="{{ route('tickets.show', $ticket) }}">Bileti aç ↗</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
