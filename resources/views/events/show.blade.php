@extends('layouts.public')

@section('title', $event->title.' · yerin.')

@section('content')
<div class="container">
    <div class="eyebrow mb-3">Etkinlikler / {{ $event->categoryLabel() }} / {{ $event->title }}</div>
    <div class="row g-4">
        <div class="col-lg-7">
            <x-event-cover :event="$event" variant="wide" class="mb-4" />
            <div class="eyebrow mb-2">{{ $event->categoryLabel() }}</div>
            <h1 class="display-title mb-3" style="font-size: 56px;">{{ $event->title }}</h1>
            <p class="muted">Şehrin sesi, denizin kıyısında.</p>
            <p class="fw-semibold">{{ format_tr_datetime($event->starts_at) }} · {{ $event->venue }}, {{ $event->city }}</p>
            <div class="soft-card mt-4">
                <h2 class="h4 fw-bold mb-3">Bu akşamın bir parçası ol.</h2>
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>
        <div class="col-lg-5">
            <div class="soft-card">
                <div class="eyebrow mb-2">Senin için bir yer var</div>
                <h2 class="display-title mb-3" style="font-size: 36px;">Biletini seç.</h2>
                <p class="muted">{{ format_tr_datetime($event->starts_at) }}</p>
                @foreach($event->ticketTypes as $type)
                    <div class="d-flex justify-content-between align-items-start py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <strong>{{ $type->name }}</strong>
                            <div class="muted small">{{ $type->description }}</div>
                        </div>
                        <strong>{{ money_tr($type->price) }}</strong>
                    </div>
                @endforeach
                <div class="info-banner my-3">{{ $event->remainingCount() }} kişilik yer kaldı. Planını şimdiden yap.</div>
                <a href="{{ route('checkout.create', $event) }}" class="btn btn-yerin btn-block-yerin">Yerini ayır ↗</a>
                <div class="muted small mt-3">Organizatör: {{ $event->organizer->name }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
