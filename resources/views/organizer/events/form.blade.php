@extends('layouts.panel')
@section('title', 'Etkinlik formu · yerin.')
@section('panel-label', isset($adminForm) ? 'Yönetim paneli' : 'Organizatör paneli')
@section('sidebar')
    @include(isset($adminForm) ? 'admin.partials.sidebar' : 'organizer.partials.sidebar', ['current' => 'events'])
@endsection
@section('content')
<div class="eyebrow">Etkinlik yönetimi</div>
<h1 class="display-title mb-2" style="font-size: 48px;">Buluşmayı tasarla.</h1>
<p class="muted mb-4">Etkinliğinin hikâyesini, yerini ve zamanını belirle.</p>
@php($action = $event->exists ? route('organizer.events.update', $event) : (isset($adminForm) ? route('admin.events.store') : route('organizer.events.store')))
<form data-ajax method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="soft-card">
                <h2 class="h4 fw-bold mb-3">Temel bilgiler</h2>
                <label class="form-label">Etkinlik adı</label>
                <input class="form-control mb-3" name="title" value="{{ old('title', $event->title) }}" required>
                <label class="form-label">Açıklama</label>
                <textarea class="form-control mb-3" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Mekân / konum</label>
                        <input class="form-control" name="venue" value="{{ old('venue', $event->venue) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Şehir</label>
                        <input class="form-control" name="city" value="{{ old('city', $event->city ?? 'İstanbul') }}" required>
                    </div>
                </div>
                <label class="form-label mt-3">Etkinlik türü</label>
                <select class="form-select" name="event_category_id" id="event-category-select" required>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" @selected((string) old('event_category_id', $event->event_category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <div class="text-danger small" data-error="event_category_id"></div>
                <div class="category-add mt-2 mb-3" data-category-create="{{ route('organizer.categories.store') }}">
                    <div class="d-flex gap-2">
                        <input class="form-control" type="text" data-category-name maxlength="80" placeholder="Yeni tür adı, örn. Konferans" autocomplete="off">
                        <button class="btn btn-ghost flex-shrink-0" type="button" data-add-category>Tür ekle</button>
                    </div>
                    <div class="small mt-2" data-category-feedback></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Başlangıç</label>
                        <input class="form-control" type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bitiş</label>
                        <input class="form-control" type="datetime-local" name="ends_at" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\\TH:i')) }}" required>
                    </div>
                </div>
                @if(isset($organizers))
                    <label class="form-label mt-3">Organizatör</label>
                    <select class="form-select" name="organizer_id">
                        @foreach($organizers as $organizer)
                            <option value="{{ $organizer->id }}">{{ $organizer->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="soft-card">
                <h2 class="h5 fw-bold mb-3">Kapak görseli</h2>
                <x-event-cover :event="$event->exists ? $event : \App\Models\Event::query()->published()->first()" />
                <label class="form-label mt-3">Görsel seç</label>
                <input class="form-control mb-2" type="file" name="cover" accept=".jpg,.jpeg,.png,.webp" data-max-kb="5120">
                <div class="text-danger small" data-error="cover"></div>
                <div class="muted small mb-3">JPG, PNG veya WebP · En fazla 5 MB</div>
                <label class="form-label">Yayın durumu</label>
                <select class="form-select mb-3" name="status">
                    @foreach(\App\Enums\EventStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $event->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Etkinliği kaydet ↗</button>
                <div class="mt-3" data-feedback></div>
            </div>
        </div>
    </div>
</form>
@if($event->exists)
    <div class="soft-card mt-4">
        <h2 class="h5 fw-bold mb-2">Etkinliği kaldır</h2>
        <p class="muted mb-3">Etkinliği, bilet tiplerini ve varsa siparişleri kalıcı olarak siler.</p>
        @include('organizer.partials.delete-event', ['event' => $event])
    </div>
@endif
@endsection
