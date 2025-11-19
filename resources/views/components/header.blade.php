<div class="header-left">
    <div class="brand-logo">
        <img src="{{ asset('images/logo-sae.jpg') }}" alt="Sae Bakery Logo" class="logo-img">
    </div>
    <h1>Sae Bakery</h1>
</div>

<div class="user-info">
    <span>Admin: <strong>{{ auth()->user()->name ?? 'User' }}</strong></span>
    <span>📅 {{ now()->format('d M Y') }}</span>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
    </form>
</div>
