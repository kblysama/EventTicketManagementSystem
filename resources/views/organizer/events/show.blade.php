@extends('layouts.panel')
@section('title', $event->title.' · yerin.')
@section('panel-label', 'Organizatör paneli')
@section('sidebar') @include('organizer.partials.sidebar', ['current' => 'events']) @endsection
@section('content')
<div class="d-flex justify-content-between align-items-start mb-2">
    <div>
        <div class="eyebrow">Etkinlik yönetimi</div>
        <h1 class="display-title" style="font-size: 52px;">{{ $event->title }}</h1>
        <p class="muted">{{ format_tr_date($event->starts_at, 'd F Y') }} · {{ $event->venue }}, {{ $event->city }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-ghost" href="{{ route('organizer.events.edit', $event) }}">Düzenle</a>
        @include('organizer.partials.delete-event', ['event' => $event])
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card stat-card--ice"><div class="stat-label">Satılan bilet</div><div class="stat-value">{{ $event->soldCount() }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Kontenjan</div><div class="stat-value">{{ $event->capacity() }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Kalan yer</div><div class="stat-value">{{ $event->remainingCount() }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Check-in</div><div class="stat-value">{{ $event->checkedInCount() }}</div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7"><x-event-cover :event="$event" variant="wide" /></div>
    <div class="col-lg-5">
        <div class="soft-card">
            <span class="status-pill mb-3">{{ $event->status->label() }}</span>
            <h2 class="h3 fw-bold mt-3">Her şey yerli yerinde.</h2>
            <p class="muted">Bilet tiplerini düzenle veya etkinliğin günü girişi doğrula.</p>
            <div class="d-flex gap-2">
                <a class="btn btn-ghost" href="{{ route('organizer.ticket-types.index', $event) }}">Bilet tipleri</a>
                <a class="btn btn-yerin" href="{{ route('organizer.check-in.show', $event) }}">Check-in ↗</a>
            </div>
            <p class="muted small mt-3">Organizatör: {{ $event->organizer->name }}</p>
        </div>
    </div>
</div>
@endsection
