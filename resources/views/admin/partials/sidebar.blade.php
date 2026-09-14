@php($current = $current ?? '')
<a class="side-link {{ $current === 'dashboard' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Genel bakış</a>
<a class="side-link {{ $current === 'users' ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">Kullanıcılar</a>
<a class="side-link {{ $current === 'events' ? 'is-active' : '' }}" href="{{ route('admin.events.index') }}">Etkinlikler</a>
<a class="side-link {{ $current === 'orders' ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}">Siparişler</a>
<a class="side-link {{ $current === 'reports' ? 'is-active' : '' }}" href="{{ route('admin.reports.sales') }}">Satış raporu</a>
