@php($sold = $event->soldCount())
<button class="btn {{ $buttonClass ?? 'btn-ghost-danger' }}" type="button" data-bs-toggle="modal" data-bs-target="#delete-event-modal">
    {{ $label ?? 'Etkinliği sil' }}
</button>
<div class="modal fade" id="delete-event-modal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content soft-card" data-ajax method="POST" action="{{ route('organizer.events.destroy', $event) }}">
            @csrf
            <h3 class="h5 fw-bold">Etkinliği sil</h3>
            <p class="muted mt-3 mb-2"><strong>{{ $event->title }}</strong> kalıcı olarak silinecek. Bu işlem geri alınamaz.</p>
            @if($sold > 0)
                <p class="muted mb-3">Bu etkinlikte {{ $sold }} satılmış bilet var. Siparişler ve biletler de silinir.</p>
            @else
                <p class="muted mb-3">Bilet tipleri de birlikte kalkar.</p>
            @endif
            <button class="btn btn-yerin btn-block-yerin" type="submit">Evet, sil</button>
            <button class="btn btn-ghost btn-block-yerin mt-2" type="button" data-bs-dismiss="modal">Vazgeç</button>
            <div class="mt-3" data-feedback></div>
        </form>
    </div>
</div>
