@extends('layouts.public')
@section('title', 'Şifremi unuttum · yerin.')
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
            <h1 class="display-title mb-3" style="font-size: 48px;">Şifreni yenileyelim.</h1>
            <p class="muted mb-4">Sıfırlama bağlantısını e-posta adresine gönderelim.</p>
            <form data-ajax method="POST" action="{{ route('password.email') }}">
                @csrf
                <label class="form-label">E-posta</label>
                <input class="form-control mb-3" type="email" name="email" required>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Bağlantıyı gönder ↗</button>
                <div class="mt-3" data-feedback></div>
            </form>
            <p class="mt-3"><a href="{{ route('login') }}">Giriş ekranına dön</a></p>
        </div>
    </div>
</div>
@endsection
