@php
    $events = $events ?? collect();
    $selected = $event;
    $routeName = $route;
@endphp
<div class="event-switcher mb-4">
    <label class="form-label" for="event-switcher">Etkinlik seç</label>
    <select id="event-switcher" class="form-select" onchange="window.location.href=this.value">
        @foreach($events as $option)
            <option value="{{ route($routeName, $option) }}" @selected($option->is($selected))>
                {{ $option->title }} · {{ format_tr_date($option->starts_at, 'd F Y') }}
            </option>
        @endforeach
    </select>
</div>
