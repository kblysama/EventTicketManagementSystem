@extends('layouts.public')
@section('title', 'Kayıt ol · yerin.')
@section('content')
<div class="container">
    <div class="row g-5 align-items-center">
        <div class="col-lg-6">
            <div class="auth-visual">
                <img src="{{ asset('images/yerin-logo.png') }}" alt="yerin." class="auth-visual-logo">
            </div>
        </div>
        <div class="col-lg-5">
            <div class="eyebrow mb-2">yerin’e hoş geldin</div>
            <h1 class="display-title mb-3" style="font-size: 48px;">Senin de yerin var.</h1>
            <p class="muted mb-4">Yeni deneyimlere ilk adımını at.</p>
            <form data-ajax method="POST" action="{{ route('register') }}">
                @csrf
                <label class="form-label">Ad soyad</label>
                <input class="form-control mb-1" name="name" required>
                <div class="text-danger small mb-3" data-error="name"></div>
                <label class="form-label">E-posta</label>
                <input class="form-control mb-1" type="email" name="email" required>
                <div class="text-danger small mb-3" data-error="email"></div>
                <label class="form-label">Şifre</label>
                <input class="form-control mb-1" type="password" name="password" required>
                <div class="text-danger small mb-3" data-error="password"></div>
                <label class="form-label">Şifre tekrar</label>
                <input class="form-control mb-3" type="password" name="password_confirmation" required>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Hesabımı oluştur ↗</button>
                <div class="mt-3" data-feedback></div>
            </form>
            <p class="mt-3"><a href="{{ route('login') }}">Giriş ekranına dön</a></p>
            <div class="info-banner mt-4">Hesabınla etkinliklere katılabilir, siparişini ve biletini tek yerden takip edebilirsin.</div>
        </div>
    </div>
</div>
@endsection
