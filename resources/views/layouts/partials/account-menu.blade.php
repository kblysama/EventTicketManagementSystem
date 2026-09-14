<div class="dropdown">
    <button class="btn btn-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ auth()->user()->firstName() }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end account-menu">
        @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
            <li><a class="dropdown-item" href="{{ route('tickets.index') }}">Biletlerim</a></li>
            <li><a class="dropdown-item" href="{{ route('orders.index') }}">Siparişlerim</a></li>
            <li><hr class="dropdown-divider"></li>
        @endif
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item" type="submit">Çıkış yap</button>
            </form>
        </li>
    </ul>
</div>
