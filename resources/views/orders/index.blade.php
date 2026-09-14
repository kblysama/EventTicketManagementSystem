@extends('layouts.public')
@section('title', 'Siparişlerim · yerin.')
@section('header-intro')
    <div class="eyebrow mb-2">Hesabım</div>
    <h1 class="display-title mb-2">Siparişlerim</h1>
    <p class="muted mb-0">Güzel anların başlangıcı burada.</p>
@endsection
@section('content')
<div class="container">
    <div class="soft-card p-0 overflow-hidden">
        <table class="yerin-table">
            <thead><tr><th>Sipariş</th><th>Etkinlik</th><th>Adet</th><th>Toplam</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>
                        <strong>{{ $order->displayNumber() }}</strong>
                        <div class="muted small">{{ format_tr_datetime($order->created_at) }}</div>
                    </td>
                    <td>{{ $order->event->title }}</td>
                    <td>{{ $order->quantity }} bilet</td>
                    <td>{{ money_tr($order->total) }}</td>
                    <td><span class="status-pill">{{ $order->status->label() }}</span></td>
                    <td class="text-end"><a href="{{ route('orders.show', $order) }}">İncele ↗</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
