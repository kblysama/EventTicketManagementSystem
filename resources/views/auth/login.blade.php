@extends('layouts.public')
@section('title', 'Giriş yap · yerin.')
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
            <h1 class="display-title mb-3" style="font-size: 48px;">Tekrar hoş geldin.</h1>
            <p class="muted mb-4">Güzel bir plana kaldığın yerden devam et.</p>
            <form data-ajax method="POST" action="{{ route('login') }}">
                @csrf
                <label class="form-label">E-posta</label>
                <input class="form-control mb-1" type="email" name="email" required>
                <div class="text-danger small mb-3" data-error="email"></div>
                <label class="form-label">Şifre</label>
                <input class="form-control mb-1" type="password" name="password" required>
                <div class="text-danger small mb-3" data-error="password"></div>
                <a href="{{ route('password.request') }}" class="d-inline-block mb-3">Şifremi unuttum</a>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Giriş yap ↗</button>
                <div class="mt-3" data-feedback></div>
            </form>
            <p class="mt-4">Henüz hesabın yok mu? <a href="{{ route('register') }}"><strong>Kayıt ol</strong></a></p>
        </div>
    </div>
</div>
@endsection
