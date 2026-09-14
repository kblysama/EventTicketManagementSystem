@extends('layouts.panel')
@section('title', 'Siparişler · yerin.')
@section('panel-label', 'Yönetim paneli')
@section('sidebar') @include('admin.partials.sidebar', ['current' => 'orders']) @endsection
@section('content')
<div class="eyebrow">Sipariş yönetimi</div>
<h1 class="display-title mb-2" style="font-size: 48px;">Tüm siparişler</h1>
<p class="muted mb-4">Etkinliklere uzanan ilk adımlar.</p>
<div class="soft-card p-0 overflow-hidden">
    <table class="yerin-table">
        <thead><tr><th>Sipariş</th><th>Alıcı</th><th>Etkinlik</th><th>Adet</th><th>Toplam</th><th>Durum</th><th></th></tr></thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td><strong>{{ $order->displayNumber() }}</strong><div class="muted small">{{ format_tr_datetime($order->created_at) }}</div></td>
                <td>{{ $order->buyer_name }}</td>
                <td>{{ $order->event->title }}</td>
                <td>{{ $order->quantity }} bilet</td>
                <td>{{ money_tr($order->total) }}</td>
                <td><span class="status-pill">{{ $order->status->label() }}</span></td>
                <td class="text-end"><a href="{{ route('admin.orders.show', $order) }}">İncele ↗</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
