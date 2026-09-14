@extends('layouts.public')

@section('title', 'Etkinlikler · yerin.')

@section('content')
<div class="container">
    @if($featured)
        <section class="hero-band mb-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="eyebrow mb-3">Şehrinde yeni bir hikâye</div>
                    <h1 class="display-title mb-3" style="font-size: clamp(48px, 6vw, 72px);">İyi ki<br>buradayım.</h1>
                    <p class="muted mb-4" style="max-width: 360px;">Müziğe, sahneye, yeni deneyimlere. Kendine bir an ayır. Yerin hazır.</p>
                    <a href="#liste" class="btn btn-yerin">Haftanın etkinliğini keşfet ↗</a>
                    <div class="small muted mt-3">İstanbul’da buluşuyoruz · {{ now()->year }}</div>
                </div>
                <div class="col-lg-6">
                    <x-event-cover :event="$featured" />
                </div>
            </div>
        </section>
    @endif

    <section id="liste" class="pt-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="display-title mb-0" style="font-size: 42px;">Bir sonraki planın.</h2>
            <div class="muted">Senin için seçtiğimiz {{ $events->count() }} etkinlik</div>
        </div>

        <form method="GET" action="{{ route('events.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <input class="form-control search-input flex-grow-1" type="search" name="q" value="{{ $search }}" placeholder="Bir etkinlik veya mekân ara...">
            <a class="filter-pill {{ !$activeCategory ? 'is-active' : '' }}" href="{{ route('events.index', ['q' => $search]) }}">Tümü</a>
            @foreach($categories as $category)
                <a class="filter-pill {{ $activeCategory === $category->slug ? 'is-active' : '' }}" href="{{ route('events.index', ['q' => $search, 'category' => $category->slug]) }}">{{ $category->name }}</a>
            @endforeach
        </form>

        <div class="row g-4">
            @foreach($events as $event)
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('events.show', $event) }}" class="event-card d-block h-100">
                        <x-event-cover :event="$event" />
                        <div class="event-card-body">
                            <div class="eyebrow mb-2">{{ $event->categoryLabel() }} / {{ format_tr_date($event->starts_at, 'd F Y') }}</div>
                            <h3 class="fw-bold mb-1">{{ $event->title }}</h3>
                            <div class="muted mb-3">{{ $event->venue }}, {{ $event->city }}</div>
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event->startingPrice() ? money_tr($event->startingPrice()).' başlayan fiyatlarla' : 'Yakında' }}</strong>
                                <span>↗</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
