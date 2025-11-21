<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="school-logo">
            <i class="fas fa-school"></i>
        </div>
        <div class="school-name">SMK Bakti Nusantara 666</div>
    </div>
    <div class="sidebar-menu">
        @if(Auth::check())
            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard.statistik') }}"
                    class="menu-item {{ request()->routeIs('dashboard.statistik') ? 'active' : '' }}">
                    <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="menu-text">Dashboard</div>
                </a>
            @endif

            <a href="/dashboard" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <div class="menu-text">Beranda</div>
            </a>

            @if (Auth::user()->isGuest())
                <!-- Tidak ada menu tambahan untuk Guest -->
            @else
                {{-- Tampilkan menu upload hanya untuk admin, guru, dan siswa --}}
                @if(Auth::user()->isAdmin() || Auth::user()->isGuru() || Auth::user()->isSiswa())
                    <a href="/upload" class="menu-item {{ request()->routeIs('upload*') ? 'active' : '' }}">
                        <div class="menu-icon"><i class="fas fa-upload"></i></div>
                        <div class="menu-text">Upload Karya</div>
                    </a>
                @endif
            @endif

            @if(Auth::user()->isGuru() || Auth::user()->isAdmin())
                <a href="{{ route('moderasi.drafts') }}"
                    class="menu-item {{ request()->routeIs('moderasi*') ? 'active' : '' }}">
                    <div class="menu-icon"><i class="fas fa-eye"></i></div>
                    <div class="menu-text">Moderasi Draft</div>
                </a>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isGuru())
                <a href="{{ route('osis.manage') }}" class="menu-item {{ request()->routeIs('osis.manage') ? 'active' : '' }}">
                    <div class="menu-icon"><i class="fas fa-users-cog"></i></div>
                    <div class="menu-text">Kelola OSIS</div>
                </a>
            @endif

            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.index') }}" class="menu-item {{ request()->routeIs('admin*') ? 'active' : '' }}">
                    <div class="menu-icon"><i class="fas fa-users-cog"></i></div>
                    <div class="menu-text">kelola user</div>
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                </button>
            </form>
        @else
            <a href="/" class="menu-item">
                <div class="menu-icon"><i class="fas fa-home"></i></div>
                <div class="menu-text">Beranda</div>
            </a>
            <a href="/login" class="menu-item">
                <div class="menu-icon"><i class="fas fa-sign-in-alt"></i></div>
                <div class="menu-text">Login</div>
            </a>
        @endif
    </div>
</div>