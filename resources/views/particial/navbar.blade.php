<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}"> <!-- Ganti href="/" menjadi route('landing') -->
            @if(file_exists(public_path('images/logo.svg')))
                <img src="{{ asset('images/logo.svg') }}" alt="Logo SMK Bakti Nusantara 666" class="navbar-logo me-2">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Bakti Nusantara 666" class="navbar-logo me-2">
            @else
                <i class="fas fa-school me-2"></i>
            @endif
            SMK Bakti Nusantara 666
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto"> <!-- Tambahkan menu navigasi kiri -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('home') }}">Beranda</a>
                </li>
            </ul>
            <div class="ms-auto d-flex flex-wrap align-items-center gap-2">
                @if(Auth::check())
                    <a href="/dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                @endif
                <a href="{{ route('osis.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-users me-1"></i> OSIS
                </a>
            </div>
        </div>
    </div>
</nav>