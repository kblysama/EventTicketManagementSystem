@props(['event', 'variant' => 'card'])

@php
    $class = match ($variant) {
        'poster' => 'event-cover event-cover--poster',
        'wide' => 'event-cover event-cover--wide',
        default => 'event-cover',
    };
@endphp

<div {{ $attributes->class($class) }}>
    @if($event?->coverUrl())
        <img src="{{ $event->coverUrl() }}" alt="{{ $event->title }}" class="cover-photo">
    @else
        <div class="cover-rings"></div>
        <div class="position-absolute top-0 start-0 p-4 cover-kicker">YERİN SUNAR / {{ $event?->categoryLabel() ?? 'ETKİNLİK' }}</div>
        <div class="position-absolute bottom-0 start-0 end-0 p-4 d-flex justify-content-between align-items-end">
            <div>
                <div class="cover-title">{{ $event?->title ? $event->displayTitle().'.' : 'YERİN.' }}</div>
                <div class="cover-meta mt-3">{{ format_tr_date($event?->starts_at, 'd F Y') }}</div>
            </div>
            <div class="cover-meta">{{ $event?->city }}</div>
        </div>
    @endif
</div>
