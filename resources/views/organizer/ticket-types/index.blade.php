@extends('layouts.panel')
@section('title', 'Bilet tipleri · yerin.')
@section('panel-label', 'Organizatör paneli')
@section('sidebar') @include('organizer.partials.sidebar', ['current' => 'tickets']) @endsection
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="eyebrow">Bilet yönetimi</div>
        <h1 class="display-title" style="font-size: 48px;">Bilet tipleri</h1>
        <p class="muted">Hangi etkinlik için bilet tipi yöneteceğini seç.</p>
    </div>
    <button class="btn btn-yerin" data-bs-toggle="modal" data-bs-target="#ticketModal">+ Bilet tipi ekle</button>
</div>
@include('organizer.partials.event-switcher', ['events' => $events, 'event' => $event, 'route' => 'organizer.ticket-types.index'])
<div class="soft-card p-0 overflow-hidden mb-3">
    <table class="yerin-table">
        <thead><tr><th>Bilet tipi</th><th>Fiyat</th><th>Satılan / Kapasite</th><th>Satış durumu</th><th></th></tr></thead>
        <tbody>
        @foreach($event->ticketTypes as $type)
            <tr>
                <td>{{ $type->name }}</td>
                <td>{{ money_tr($type->price) }}</td>
                <td>{{ $type->soldCount() }} / {{ $type->capacity }}</td>
                <td><span class="status-pill">{{ $type->sale_status->label() }}</span></td>
                <td class="text-end">
                    <button class="btn btn-ghost btn-sm" data-bs-toggle="modal" data-bs-target="#edit-{{ $type->id }}">Düzenle</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="info-banner">Satılmış biletleri olan bir bilet tipi silinemez. Yeni alımları durdurmak için satışı kapatabilirsin.</div>

<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content soft-card" data-ajax method="POST" action="{{ route('organizer.ticket-types.store', $event) }}">
            @csrf
            <h3 class="h5 fw-bold">Yeni bilet tipi</h3>
            <label class="form-label mt-3">Ad</label>
            <input class="form-control mb-3" name="name" required>
            <label class="form-label">Açıklama</label>
            <input class="form-control mb-3" name="description">
            <label class="form-label">Fiyat</label>
            <input class="form-control mb-3" type="number" name="price" min="0" required>
            <label class="form-label">Kapasite</label>
            <input class="form-control mb-3" type="number" name="capacity" min="1" required>
            <input type="hidden" name="sale_status" value="on_sale">
            <button class="btn btn-yerin btn-block-yerin" type="submit">Kaydet ↗</button>
            <div class="mt-2" data-feedback></div>
        </form>
    </div>
</div>

@foreach($event->ticketTypes as $type)
<div class="modal fade" id="edit-{{ $type->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content soft-card" data-ajax method="POST" action="{{ route('organizer.ticket-types.update', [$event, $type]) }}">
            @csrf
            <h3 class="h5 fw-bold">{{ $type->name }}</h3>
            <label class="form-label mt-3">Ad</label>
            <input class="form-control mb-3" name="name" value="{{ $type->name }}" required>
            <label class="form-label">Açıklama</label>
            <input class="form-control mb-3" name="description" value="{{ $type->description }}">
            <label class="form-label">Fiyat</label>
            <input class="form-control mb-3" type="number" name="price" value="{{ $type->price }}" required>
            <label class="form-label">Kapasite</label>
            <input class="form-control mb-3" type="number" name="capacity" value="{{ $type->capacity }}" required>
            <label class="form-label">Satış durumu</label>
            <select class="form-select mb-3" name="sale_status">
                @foreach(\App\Enums\TicketSaleStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($type->sale_status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button class="btn btn-yerin btn-block-yerin" type="submit">Güncelle ↗</button>
        </form>
    </div>
</div>
@endforeach
@endsection
