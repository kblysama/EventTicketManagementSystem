@extends('layouts.public')
@section('title', 'Bilet satın al · yerin.')
@section('content')
<div class="container">
    <div class="eyebrow mb-2">Yerini ayır</div>
    <h1 class="display-title mb-2" style="font-size: 52px;">Güzel bir plan yapıyorsun.</h1>
    <p class="muted mb-4">Biletlerini seç, bilgilerini kontrol et.</p>
    <div class="row g-4">
        <div class="col-lg-7">
            <form class="soft-card" data-ajax method="POST" action="{{ route('checkout.store', $event) }}">
                @csrf
                <h2 class="h4 fw-bold mb-3">Bilet seçimi</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Bilet tipi</label>
                        <select class="form-select" name="ticket_type_id" id="ticketType">
                            @foreach($event->ticketTypes as $type)
                                <option value="{{ $type->id }}" data-price="{{ $type->price }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adet</label>
                        <input class="form-control" type="number" name="quantity" id="quantity" value="2" min="1" max="10">
                    </div>
                </div>
                <h2 class="h4 fw-bold mb-3">Katılımcı bilgileri</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Ad soyad</label>
                        <input class="form-control" name="buyer_name" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-posta</label>
                        <input class="form-control" type="email" name="buyer_email" value="{{ auth()->user()->email }}" required>
                    </div>
                </div>
                <div class="info-banner mb-3">Bu demo satın alma işlemidir. Kart bilgisi istenmez ve gerçek ödeme alınmaz.</div>
                <button class="btn btn-yerin btn-block-yerin" type="submit">Satın almayı tamamla ↗</button>
                <div class="mt-3" data-feedback></div>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="soft-card">
                <x-event-cover :event="$event" />
                <h3 class="fw-bold mt-3 mb-1">{{ $event->title }}</h3>
                <p class="muted">{{ format_tr_date($event->starts_at, 'd F Y') }} · {{ $event->venue }}, {{ $event->city }}</p>
                <div class="d-flex justify-content-between">
                    <span id="summaryLine">2 × Standart</span>
                    <strong id="summaryTotal">{{ money_tr(($event->ticketTypes->first()?->price ?? 0) * 2) }}</strong>
                </div>
                <p class="muted small mt-3">Biletlerin işlem tamamlandığında hesabında görünecek.</p>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('input', () => {
    const select = document.getElementById('ticketType');
    const qty = Number(document.getElementById('quantity').value || 1);
    const option = select.options[select.selectedIndex];
    document.getElementById('summaryLine').textContent = qty + ' × ' + option.textContent;
    document.getElementById('summaryTotal').textContent = new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(Number(option.dataset.price) * qty);
});
</script>
@endsection
