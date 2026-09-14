<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'yerin.')</title>
    <link rel="icon" href="{{ asset('images/yerin-logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/yerin-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="site-edge d-flex align-items-start justify-content-between">
            <div class="header-brand">
                <a href="{{ route('events.index') }}"><img src="{{ asset('images/yerin-logo.png') }}" alt="yerin." class="brand-logo"></a>
                @hasSection('header-intro')
                    <div class="header-intro">
                        @yield('header-intro')
                    </div>
                @endif
            </div>
            <nav class="site-header-nav d-flex align-items-center">
                <a class="nav-link-quiet d-none d-md-inline" href="{{ route('events.index') }}">Etkinlikleri keşfet</a>
                @unless(request()->routeIs(['login', 'register', 'password.request', 'password.reset', 'password.email', 'password.update']))
                    <a class="nav-link-quiet" href="{{ route('tickets.index') }}">Biletlerim</a>
                    <a class="nav-link-quiet" href="{{ route('orders.index') }}">Siparişlerim</a>
                    @auth
                        @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
                            <a class="nav-link-quiet d-none d-md-inline" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('organizer.dashboard') }}">Panel</a>
                        @endif
                        <div class="ms-2">@include('layouts.partials.account-menu')</div>
                    @else
                        <a class="btn btn-ghost ms-2" href="{{ route('login') }}">Giriş yap</a>
                    @endauth
                @endunless
            </nav>
        </div>
    </header>

    <main class="site-main pb-5">
        @yield('content')
    </main>

    @include('layouts.partials.site-footer')
</body>
</html>
