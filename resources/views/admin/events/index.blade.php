@extends('layouts.panel')
@section('title', 'Tüm etkinlikler · yerin.')
@section('panel-label', 'Yönetim paneli')
@section('sidebar') @include('admin.partials.sidebar', ['current' => 'events']) @endsection
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="eyebrow">Etkinlik yönetimi</div>
        <h1 class="display-title" style="font-size: 48px;">Tüm etkinlikler</h1>
        <p class="muted">Her buluşmanın hazırlığı burada başlar.</p>
    </div>
    <a class="btn btn-yerin" href="{{ route('admin.events.create') }}">+ Etkinlik oluştur</a>
</div>
<form class="d-flex gap-2 mb-4" method="GET">
    <input class="form-control" name="q" value="{{ $search }}" placeholder="Etkinlik ara...">
    <select class="form-select" name="status" style="max-width:220px" onchange="this.form.submit()">
        <option value="">Tüm durumlar</option>
        <option value="published" @selected($status === 'published')>Yayında</option>
        <option value="draft" @selected($status === 'draft')>Taslak</option>
    </select>
</form>
<div class="soft-card p-0 overflow-hidden">
    <table class="yerin-table">
        <thead><tr><th>Etkinlik</th><th>Tarih</th><th>Organizatör</th><th>Durum</th><th>Satış / Kontenjan</th><th></th></tr></thead>
        <tbody>
        @foreach($events as $event)
            <tr>
                <td><strong>{{ $event->title }}</strong><div class="muted small">{{ $event->venue }}, {{ $event->city }}</div></td>
                <td>{{ format_tr_date($event->starts_at, 'd F Y') }}</td>
                <td>{{ $event->organizer->name }}</td>
                <td><span class="status-pill">{{ $event->status->label() }}</span></td>
                <td>{{ $event->soldCount() }} / {{ $event->capacity() }}</td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('events.show', $event) }}">Görüntüle ↗</a>
                    <form class="d-inline ms-3" data-ajax data-confirm="{{ $event->title }} etkinliğini silmek istediğine emin misin?" method="POST" action="{{ route('admin.events.destroy', $event) }}">
                        @csrf
                        <button class="btn btn-link p-0 align-baseline" type="submit">Sil</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
