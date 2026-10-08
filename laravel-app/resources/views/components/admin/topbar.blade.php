<nav class="navbar d-flex justify-content-between align-items-center">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
        <i class="bi bi-heart-pulse-fill"></i>
        RehabPlus
    </a>

    <div class="d-flex align-items-center gap-3">
        <span class="top-date d-none d-md-inline">{{ now()->format('F j, Y') }}</span>
        <button type="button" class="theme-toggle" id="themeToggle" title="Toggle dark mode" aria-label="Toggle dark mode">
            <i class="bi bi-moon-fill"></i>
        </button>
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div>
                <div class="fw-bold">{{ auth()->user()->name ?? '' }}</div>
                <small class="text-muted">{{ ucfirst(auth()->user()->role ?? '') }}</small>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>
                Logout
            </button>
        </form>
    </div>
</nav>