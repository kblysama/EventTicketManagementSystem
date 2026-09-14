@extends('layouts.panel')
@section('title', 'Yönetim · yerin.')
@section('panel-label', 'Yönetim paneli')
@section('sidebar') @include('admin.partials.sidebar', ['current' => 'dashboard']) @endsection
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="eyebrow">Yönetim paneli</div>
        <h1 class="display-title" style="font-size: 48px;">Yerin, bir bakışta.</h1>
        <p class="muted">İyi deneyimler, iyi bir hazırlıkla başlar.</p>
    </div>
    <a class="btn btn-yerin" href="{{ route('admin.events.create') }}">+ Etkinlik oluştur</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card stat-card--ice"><div class="stat-label">Toplam satış</div><div class="stat-value">{{ money_tr($totalSales) }}</div><div class="muted small">Simüle satış tutarı</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Satılan bilet</div><div class="stat-value">{{ $soldTickets }}</div><div class="muted small">{{ $activeEvents }} etkinlikte</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Aktif etkinlik</div><div class="stat-value">{{ $activeEvents }}</div><div class="muted small">{{ $totalCapacity }} toplam kontenjan</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Kullanıcı</div><div class="stat-value">{{ $usersCount }}</div><div class="muted small">3 farklı rol</div></div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="soft-card"><div class="d-flex justify-content-between mb-3"><strong>Etkinliklere göre satış</strong><span class="muted">Bilet adedi</span></div><canvas data-chart='@json($chart)' height="160"></canvas></div></div>
    <div class="col-lg-4">
        @if($nextEvent)
            <div class="soft-card">
                <div class="eyebrow mb-2">Sıradaki buluşma</div>
                <h2 class="h3 fw-bold">{{ $nextEvent->title }}</h2>
                <p class="muted mb-1">{{ format_tr_datetime($nextEvent->starts_at) }}</p>
                <p class="muted">{{ $nextEvent->venue }}</p>
                <p>{{ $nextEvent->soldCount() }} katılımcı yerini hazır.</p>
                <a class="btn btn-ghost" href="{{ route('organizer.events.show', $nextEvent) }}">Etkinliği yönet ↗</a>
            </div>
        @endif
    </div>
</div>
<h2 class="h4 fw-bold mb-3">Etkinliklerin durumu</h2>
<div class="soft-card p-0 overflow-hidden">
    <table class="yerin-table">
        <thead><tr><th>Etkinlik</th><th>Tarih</th><th>Durum</th><th>Satış / Kontenjan</th><th></th></tr></thead>
        <tbody>
        @foreach($events as $event)
            <tr>
                <td><strong>{{ $event->title }}</strong><div class="muted small">{{ $event->venue }}, {{ $event->city }}</div></td>
                <td>{{ format_tr_date($event->starts_at, 'd F Y') }}</td>
                <td><span class="status-pill">{{ $event->status->label() }}</span></td>
                <td>{{ $event->soldCount() }} / {{ $event->capacity() }}</td>
                <td class="text-end"><a href="{{ route('organizer.events.show', $event) }}">Yönet ↗</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
