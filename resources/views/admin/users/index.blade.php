@extends('layouts.panel')
@section('title', 'Kullanıcılar · yerin.')
@section('panel-label', 'Yönetim paneli')
@section('sidebar') @include('admin.partials.sidebar', ['current' => 'users']) @endsection
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <div class="eyebrow">Kullanıcı yönetimi</div>
        <h1 class="display-title" style="font-size: 48px;">Yerin’in insanları.</h1>
        <p class="muted">Hesap oluştur, rol değiştir, düzenle veya sil.</p>
    </div>
    <button class="btn btn-yerin" data-bs-toggle="modal" data-bs-target="#userCreateModal">+ Kullanıcı oluştur</button>
</div>
<form class="mb-4" method="GET"><input class="form-control" name="q" value="{{ $search }}" placeholder="Ad veya e-posta ara..."></form>
@if ($errors->any())
    <div class="info-banner mb-3 text-danger">{{ $errors->first() }}</div>
@endif
<div class="soft-card p-0 overflow-hidden">
    <table class="yerin-table">
        <thead><tr><th>Kullanıcı</th><th>Rol</th><th>Kayıt tarihi</th><th class="text-end">İşlemler</th></tr></thead>
        <tbody>
        @foreach($users as $user)
            @php($isSelf = $user->id === auth()->id())
            @php($isLastAdmin = $user->isAdmin() && $adminCount <= 1)
            <tr>
                <td>
                    <strong>{{ $user->name }}</strong>
                    <div class="muted small">{{ $user->email }}</div>
                    @if($user->organized_events_count)
                        <div class="muted small">{{ $user->organized_events_count }} etkinlik</div>
                    @endif
                </td>
                <td>
                    @if($isSelf)
                        <span class="status-pill">{{ $user->role->label() }}</span>
                        <div class="muted small mt-1">Bu sizsiniz</div>
                    @else
                        <form class="d-inline" data-ajax method="POST" action="{{ route('admin.users.role', $user) }}">
                            @csrf
                            <select name="role" class="form-select d-inline-block" style="width:auto; min-width: 9rem;" onchange="this.form.requestSubmit()" @disabled($isLastAdmin)>
                                @foreach($roles as $role)
                                    <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </form>
                        @if($isLastAdmin)
                            <div class="muted small mt-1">Son admin</div>
                        @endif
                    @endif
                </td>
                <td>{{ format_tr_date($user->created_at, 'd F Y') }}</td>
                <td class="text-end text-nowrap">
                    <button
                        class="btn btn-ghost"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#userEditModal-{{ $user->id }}"
                    >Düzenle</button>
                    @unless($isSelf)
                        <form class="d-inline" data-ajax data-confirm="{{ $user->name }} hesabını silmek istediğine emin misin?" method="POST" action="{{ route('admin.users.destroy', $user) }}">
                            @csrf
                            <button class="btn btn-ghost-danger" type="submit" @disabled($isLastAdmin || $user->organized_events_count > 0)>Sil</button>
                        </form>
                    @endunless
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="userCreateModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content soft-card" data-ajax method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <h3 class="h5 fw-bold">Yeni kullanıcı</h3>
            <label class="form-label mt-3">Ad soyad</label>
            <input class="form-control mb-1" name="name" required>
            <div class="text-danger small mb-2" data-error="name"></div>
            <label class="form-label">E-posta</label>
            <input class="form-control mb-1" type="email" name="email" required>
            <div class="text-danger small mb-2" data-error="email"></div>
            <label class="form-label">Şifre</label>
            <input class="form-control mb-1" type="password" name="password" required>
            <div class="text-danger small mb-2" data-error="password"></div>
            <label class="form-label">Rol</label>
            <select class="form-select mb-3" name="role">
                @foreach($roles as $role)
                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                @endforeach
            </select>
            <button class="btn btn-yerin btn-block-yerin" type="submit">Oluştur ↗</button>
            <div class="mt-2" data-feedback></div>
        </form>
    </div>
</div>

@foreach($users as $user)
    @php($isSelf = $user->id === auth()->id())
    @php($isLastAdmin = $user->isAdmin() && $adminCount <= 1)
    <div class="modal fade" id="userEditModal-{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content soft-card" data-ajax method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                <h3 class="h5 fw-bold">Kullanıcıyı düzenle</h3>
                <p class="muted small mb-0">{{ $user->email }}</p>
                <label class="form-label mt-3">Ad soyad</label>
                <input class="form-control mb-1" name="name" value="{{ $user->name }}" required>
                <div class="text-danger small mb-2" data-error="name"></div>
                <label class="form-label">E-posta</label>
                <input class="form-control mb-1" type="email" name="email" value="{{ $user->email }}" required>
                <div class="text-danger small mb-2" data-error="email"></div>
                <label class="form-label">Yeni şifre <span class="muted">(opsiyonel)</span></label>
                <input class="form-control mb-1" type="password" name="password" autocomplete="new-password">
                <div class="text-danger small mb-2" data-error="password"></div>
                <label class="form-label">Rol</label>
                <select class="form-select mb-3" name="role" @disabled($isSelf || $isLastAdmin)>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                @if($isSelf || $isLastAdmin)
                    <input type="hidden" name="role" value="{{ $user->role->value }}">
                @endif
                <button class="btn btn-yerin btn-block-yerin" type="submit">Kaydet ↗</button>
                <div class="mt-2" data-feedback></div>
            </form>
        </div>
    </div>
@endforeach
@endsection
