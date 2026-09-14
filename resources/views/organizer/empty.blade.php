@extends('layouts.panel')
@section('title', ($heading ?? 'Organizatör').' · yerin.')
@section('panel-label', 'Organizatör paneli')
@section('sidebar') @include('organizer.partials.sidebar', ['current' => $current ?? '']) @endsection
@section('content')
<div class="eyebrow">{{ $eyebrow }}</div>
<h1 class="display-title mb-2" style="font-size: 48px;">{{ $heading }}</h1>
<p class="muted mb-4">{{ $copy }}</p>
<a class="btn btn-yerin" href="{{ route('organizer.events.create') }}">+ Etkinlik oluştur</a>
@endsection
