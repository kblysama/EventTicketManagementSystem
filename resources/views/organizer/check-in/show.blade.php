@extends('layouts.panel')
@section('title', 'Check-in · yerin.')
@section('panel-label', 'Organizatör paneli')
@section('sidebar') @include('organizer.partials.sidebar', ['current' => 'checkin']) @endsection
@section('content')
<div class="eyebrow">Giriş doğrulama</div>
<h1 class="display-title mb-2" style="font-size: 48px;">Hoş geldiniz, diyelim.</h1>
<p class="muted mb-4">Hangi etkinlik için giriş doğrulayacağını seç.</p>
@include('organizer.partials.event-switcher', ['events' => $events, 'event' => $event, 'route' => 'organizer.check-in.show'])
<div class="soft-card mx-auto text-center" style="max-width: 560px;">
    <div class="checkin-icon mb-3">↗</div>
    <h2 class="h3 fw-bold">Bilet kodunu doğrula</h2>
    <p class="muted">Katılımcının biletindeki kodu aşağıya gir.</p>
    <form data-ajax method="POST" action="{{ route('organizer.check-in.store', $event) }}">
        @csrf
        <label class="form-label text-start d-block">Bilet kodu</label>
        <input class="form-control mb-3" name="code" placeholder="YR-KC-8F2A" required>
        <button class="btn btn-yerin btn-block-yerin" type="submit">Girişi doğrula ↗</button>
        <div class="mt-3" data-feedback></div>
    </form>
</div>
@endsection
