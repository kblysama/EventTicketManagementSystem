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
    <div class="panel-shell d-flex">
        <aside class="panel-sidebar">
            <a href="{{ route('events.index') }}" class="panel-brand">
                <img src="{{ asset('images/yerin-logo.png') }}" alt="yerin." class="brand-logo">
            </a>
            <nav class="panel-nav">
                @yield('sidebar')
            </nav>
            <div class="panel-user eyebrow mb-0">{{ auth()->user()->name }}</div>
        </aside>
        <div class="panel-main">
            <div class="panel-header">
                <span class="eyebrow mb-0">@yield('panel-label')</span>
                <a class="nav-link-quiet" href="{{ route('events.index') }}">Siteye git ↗</a>
                @include('layouts.partials.account-menu')
            </div>
            @yield('content')
        </div>
    </div>
</body>
</html>
