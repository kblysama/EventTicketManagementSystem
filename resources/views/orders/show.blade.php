@extends('layouts.public')
@section('title', $order->displayNumber().' · yerin.')
@section('header-intro')
    <div class="eyebrow mb-2">Siparişlerim</div>
    <h1 class="display-title mb-2">{{ $order->displayNumber() }}</h1>
    <p class="muted mb-0">{{ format_tr_datetime($order->created_at) }}</p>
@endsection
@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-4">
        <span class="status-pill">{{ $order->status->label() }}</span>
    </div>
    <div class="info-banner mb-4">Yerin hazır. {{ $order->quantity }} bilet oluşturuldu. Bu sipariş simüle alma ile tamamlandı.</div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="soft-card">
                <h2 class="h4 fw-bold">{{ $order->event->title }}</h2>
                <p class="muted">{{ format_tr_date($order->event->starts_at, 'd F Y') }} · {{ $order->event->venue }}, {{ $order->event->city }}</p>
                <table class="yerin-table">
                    <thead><tr><th>Bilet tipi</th><th>Adet</th><th>Birim fiyat</th><th>Toplam</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->ticketType->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ money_tr($order->unit_price) }}</td>
                            <td>{{ money_tr($order->total) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-3"><span>Sipariş toplamı</span><strong>{{ money_tr($order->total) }}</strong></div>
                <a href="{{ route('tickets.show', $order->tickets->first()) }}" class="btn btn-yerin mt-4">Bileti görüntüle ↗</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="soft-card">
                <h3 class="h5 fw-bold">Alıcı bilgileri</h3>
                <p class="mb-1">{{ $order->buyer_name }}</p>
                <p class="muted">{{ $order->buyer_email }}</p>
                <h3 class="h5 fw-bold mt-4">Bilet kodları</h3>
                @foreach($order->tickets as $ticket)
                    <div class="fw-bold">{{ $ticket->code }}</div>
                @endforeach
                <p class="muted small mt-3">Her bilet tek kişiliktir ve bir kez kullanılır.</p>
            </div>
        </div>
    </div>
</div>
@endsection
