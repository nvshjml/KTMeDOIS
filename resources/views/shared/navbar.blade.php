<nav class="navbar ktm-topbar px-3 px-lg-4">
    <div class="d-flex align-items-center gap-2">
        <img class="ktm-logo-sm" src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
        <div>
            <div class="fw-bold">KTM eDOIS</div>
            <div class="small text-muted">Electronic Delivery Order &amp; Invoice System</div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        @auth
            <span class="small text-muted d-none d-sm-inline">{{ auth()->user()->username }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
            </form>
        @else
            @if(session('supplier_id'))
                <span class="small text-muted d-none d-sm-inline">Supplier Session</span>
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Supplier Logout</button>
                </form>
            @else
                <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Customer Login</a>
                <a class="btn btn-warning btn-sm" href="{{ route('supplier.verify') }}">Supplier Verify</a>
            @endif
        @endauth
    </div>
</nav>
