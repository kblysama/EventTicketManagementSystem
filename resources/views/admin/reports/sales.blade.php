@extends('layouts.panel')
@section('title', 'Satış raporu · yerin.')
@section('panel-label', 'Yönetim paneli')
@section('sidebar') @include('admin.partials.sidebar', ['current' => 'reports']) @endsection
@section('content')
<div class="eyebrow">Raporlama</div>
<h1 class="display-title mb-2" style="font-size: 48px;">Anların sayılara yansıması.</h1>
<p class="muted mb-4">Etkinlik bazında satış ve katılım özeti.</p>
<div class="info-banner mb-4">Gösterilen tutarlar demo ortamındaki simüle satışı temsil eder.</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card stat-card--ice"><div class="stat-label">Toplam satış</div><div class="stat-value">{{ money_tr($totalSales) }}</div><div class="muted small">Simüle satış tutarı</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Satılan bilet</div><div class="stat-value">{{ $soldTickets }}</div><div class="muted small">{{ $activeEvents }} etkinlikte</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Aktif etkinlik</div><div class="stat-value">{{ $activeEvents }}</div><div class="muted small">{{ $totalCapacity }} toplam kontenjan</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Check-in</div><div class="stat-value">{{ $checkedIn }}</div><div class="muted small">{{ $checkedIn ? 'Giriş doğrulandı' : 'Henüz giriş yapılmadı' }}</div></div></div>
</div>
<div class="soft-card p-0 overflow-hidden">
    <table class="yerin-table">
        <thead><tr><th>Etkinlik</th><th>Satılan</th><th>Satış tutarı</th><th>Kalan</th><th>Check-in</th></tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['event']->title }}</td>
                <td>{{ $row['sold'] }}</td>
                <td>{{ money_tr($row['revenue']) }}</td>
                <td>{{ $row['remaining'] }}</td>
                <td>{{ $row['checked_in'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td><strong>Toplam</strong></td>
            <td colspan="4" class="text-end"><strong>{{ $soldTickets }} bilet · {{ money_tr($totalSales) }}</strong></td>
        </tr>
        </tbody>
    </table>
</div>
@endsection
