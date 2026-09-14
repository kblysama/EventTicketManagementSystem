@extends('layouts.public')
@section('title', 'Şifre sıfırla · yerin.')
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
            <h1 class="display-title mb-3" style="font-size: 48px;">Yeni bir başlangıç.</h1>
            <p class="muted mb-4">Hesabın için yeni şifre belirle.</p>
            <form data-ajax method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <label class="form-label">Yeni şifre</label>
                <input class="form-control mb-3" type="password" name="password" required>
                <label class="form-label">Şifre tekrar</label>
                <input class="form-control mb-3" type="password" name="password_confirmation" required>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Şifremi güncelle ↗</button>
                <div class="mt-3" data-feedback></div>
            </form>
            <p class="mt-3"><a href="{{ route('login') }}">Giriş ekranına dön</a></p>
        </div>
    </div>
</div>
@endsection
