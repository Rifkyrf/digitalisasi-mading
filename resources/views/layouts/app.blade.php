<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Bakti Nusantara 666 - @yield('title')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- alert css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('css/mobile-bottom-nav.css') }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')


</head>

<body>

    <!-- Topbar -->
    <div class="topbar">
        <!-- Brand & Menu Toggle -->
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light me-3 d-lg-none" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="topbar-brand">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="SMK Bakti Nusantara 666" class="topbar-logo">
                @else
                    <i class="fas fa-school me-2"></i>
                @endif
                <span>SMK Bakti Nusantara 666</span>
            </a>
        </div>

           <!-- Search Bar (Desktop Only) -->
    <div class="search-bar d-none d-lg-block">
        <div class="container d-flex justify-content-center">
            <div class="position-relative" style="max-width: 500px; width: 100%;">
                <div class="input-group search-input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="desktopSearchInput" class="form-control search-input border-0"
                           placeholder="Cari pengguna, konten, atau informasi..." autocomplete="off">
                    <button class="btn btn-primary search-btn" id="desktopSearchButton" disabled>
                        <i class="fas fa-spinner fa-spin d-none" id="desktopSearchSpinner"></i>
                        Cari
                    </button>
                </div>
                <div id="desktopSearchResults" class="position-absolute bg-white shadow rounded mt-1 w-100"
                     style="z-index: 9999; display: none; max-height: 400px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>

        <!-- Topbar Actions -->
        <div class="topbar-actions">
            <!-- Notifications Dropdown -->
            @if(Auth::check())
            <div class="dropdown">
                <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if (Auth::user()->unreadNotifications()->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ Auth::user()->unreadNotifications()->count() }}
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="max-height: 400px; overflow-y: auto;">
                    @forelse (Auth::user()->notifications()->latest()->take(10)->get() as $notification) <!-- Ambil 10 terbaru -->
                        <li class="notification-item {{ $notification->read ? '' : 'unread' }}">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ $notification->url ?? '#' }}" onclick="markAsRead('{{ $notification->id }}', this)">
                                <div>
                                    <h6 class="mb-0">{{ $notification->title }}</h6>
                                    <small class="text-muted">{{ $notification->message }}</small><br>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if (!$notification->read)
                                    <span class="badge bg-success rounded-pill">Baru</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li><a class="dropdown-item text-center text-muted" href="#">Tidak ada notifikasi</a></li>
                    @endforelse
                </ul>
            </div>
            @endif

            <!-- User Profile Dropdown -->
            @if(Auth::check())
            <div class="dropdown ms-2"> <!-- Tambahkan margin kiri -->
                <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown">
                    <img src="{{ Auth::user()->profile_photo
                        ? asset('storage/' . Auth::user()->profile_photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1a4b8c&color=fff&size=64' }}"
                        alt="Foto Profil"
                        class="rounded-circle"
                        style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <img src="{{ Auth::user()->profile_photo
                                ? asset('storage/' . Auth::user()->profile_photo)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1a4b8c&color=fff&size=48' }}"
                                class="rounded-circle me-3"
                                style="width: 45px; height: 45px; object-fit: cover;">
                            <div>
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                            </div>
                        </div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.show', Auth::id()) }}"><i class="fas fa-user me-2"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-home me-2"></i> Beranda</a></li>
                    @if(Auth::user()->isAdmin())
                    <li><a class="dropdown-item" href="/admin"><i class="fas fa-cog me-2"></i> Admin Panel</a></li>
                    @endif
                    <!-- Ganti dropdown bersarang dengan satu item -->
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('password.otp.request') }}">Reset via OTP</a> |
                        <a href="{{ route('password.request') }}">Reset via Email</a>
                    </li>
                    <!-- /Ganti dropdown bersarang -->
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        @endif
        </div>
    </div>



    @include('particial.sidebar')

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <main class="content" id="mainContent">
        @yield('content')
    </main>

<!-- Mobile Bottom Navigation -->
@auth
<nav class="mobile-bottom-nav d-lg-none">
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    {{-- Sembunyikan tombol upload jika user adalah Guest --}}
    @if(!Auth::user()->isGuest())
    <a href="{{ route('upload.store') }}" class="nav-item {{ request()->routeIs('upload*') ? 'active' : '' }}">
        <i class="fas fa-plus"></i>
        <span>Upload</span>
    </a>
    @endif
    <a href="#" id="search-tab" class="nav-item">
        <i class="fas fa-search"></i>
        <span>Cari</span>
    </a>
    <a href="{{ route('profile.show', Auth::id()) }}" class="nav-item {{ request()->is('profile/*') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>Profil</span>
    </a>
</nav>
@endauth

    <!-- Modal Pencarian (Mobile) -->
    <div class="search-modal" id="searchModal">
        <div class="search-header">
            <button class="btn btn-link text-dark" id="closeSearch">&times;</button>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pengguna...">
        </div>
        <div class="search-results" id="searchResults">
            <!-- Hasil akan dimuat di sini -->
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Script Asset -->
    {{-- logika searchbar --}}
    <script src="{{ asset('javascript/app.js') }}" defer></script>
    {{-- logika untuk modal content --}}
    <script src="{{ asset('javascript/detail-karya.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- animasi --}}
    <script src="{{ asset('javascript/appn.js') }}"></script>
    {{-- script untuk memunculkan notifikasi keberhasil/error --}}
    @if(session('error'))
    <script>
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    </script>
@endif

@if($errors->any())
    <script>
        Swal.fire({
            title: 'Gagal Validasi!',
            html: `
                <ul class="text-start" style="color: #dc3545; list-style: none; padding: 0; font-size: 0.9em;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    </script>
@endif

@if(session('success'))
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });
    </script>
@endif



    @stack('scripts')
    @yield('scripts')
</body>
</html>
