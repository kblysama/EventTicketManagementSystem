@php($current = $current ?? '')
<a class="side-link {{ $current === 'dashboard' ? 'is-active' : '' }}" href="{{ route('organizer.dashboard') }}">Genel bakış</a>
<a class="side-link {{ $current === 'events' ? 'is-active' : '' }}" href="{{ route('organizer.events.index') }}">Etkinliklerim</a>
<a class="side-link {{ $current === 'tickets' ? 'is-active' : '' }}" href="{{ route('organizer.ticket-types.home') }}">Bilet tipleri</a>
<a class="side-link {{ $current === 'checkin' ? 'is-active' : '' }}" href="{{ route('organizer.check-in.home') }}">Check-in</a>
