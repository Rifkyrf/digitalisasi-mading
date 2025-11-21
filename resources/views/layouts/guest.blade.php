<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakti Nusantara 666 - @yield('title')</title>
    <!-- ✅ Perbaikan: Hapus spasi ekstra di URL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Default Theme (Blue) */
        :root {
            --primary: #0d47a1;
            --primary-dark: #0b3c85;
            --sidebar-bg: var(--primary);
            --sidebar-hover: var(--primary-dark);
            --topbar-bg: var(--primary);
            --text-color: white;
        }

        /* Light Theme */
        [data-theme="light"] {
            --primary: #4a89dc;
            --primary-dark: #3b78ca;
            --sidebar-bg: #f8f9fa;
            --sidebar-hover: #e9ecef;
            --topbar-bg: #ffffff;
            --text-color: #333;
        }

        /* Green Theme */
        [data-theme="green"] {
            --primary: #27ae60;
            --primary-dark: #229954;
            --sidebar-bg: var(--primary);
            --sidebar-hover: var(--primary-dark);
            --topbar-bg: var(--primary);
            --text-color: white;
        }

        /* Purple Theme */
        [data-theme="purple"] {
            --primary: #8e44ad;
            --primary-dark: #7d3c98;
            --sidebar-bg: var(--primary);
            --sidebar-hover: var(--primary-dark);
            --topbar-bg: var(--primary);
            --text-color: white;
        }

        /* Dark Theme */
        [data-theme="dark"] {
            --primary: #2c3e50;
            --primary-dark: #1a252f;
            --sidebar-bg: #1a1a1a;
            --sidebar-hover: #2c2c2c;
            --topbar-bg: #222;
            --text-color: #e0e0e0;
        }

        /* Blue Theme (explicitly defined) */
        [data-theme="blue"] {
            --primary: #0d47a1;
            --primary-dark: #0b3c85;
            --sidebar-bg: var(--primary);
            --sidebar-hover: var(--primary-dark);
            --topbar-bg: var(--primary);
            --text-color: white;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            transition: margin-left 0.3s ease;
        }

        .topbar {
            background: var(--topbar-bg);
            color: var(--text-color);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--text-color);
            position: fixed;
            top: 0;
            left: 0;
            transform: translateX(-100%);
            z-index: 1000;
            padding-top: 60px;
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                background-color 0.3s ease,
                color 0.3s ease;
            will-change: transform, background-color, color;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.12);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar a {
            color: var(--text-color);
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a:hover {
            background: var(--sidebar-hover);
        }

        /* Dropdown styling */
        .dropdown-menu {
            --bs-dropdown-link-color: var(--text-color);
            --bs-dropdown-bg: var(--sidebar-bg);
            min-width: 180px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 0.9rem;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .overlay.show {
            display: block;
            opacity: 1;
        }

        .content {
            margin-left: 0;
            min-height: 100vh;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 992px) {
            .content {
                margin-left: 250px;
            }

            .sidebar {
                transform: translateX(0);
            }

            .overlay {
                display: none !important;
            }
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-light {
            border-color: rgba(0, 0, 0, 0.2) !important;
            color: var(--text-color) !important;
        }

        [data-theme="light"] .btn-outline-light {
            border-color: rgba(0, 0, 0, 0.3) !important;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Hover effect for topbar icons */
        .topbar a:hover,
        .topbar button:hover {
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        /* ========== OVERRIDE BOOTSTRAP PRIMARY BUTTON ========== */
        .btn.btn-primary,
        .btn.btn-primary:link,
        .btn.btn-primary:visited {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: var(--text-color) !important;
        }

        .btn.btn-primary:hover,
        .btn.btn-primary:focus,
        .btn.btn-primary:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: var(--text-color) !important;
        }

        /* ========== FLOATING BUTTON ========== */
        .fixed-upload-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            transition: all 0.3s ease;
            border: none !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .fixed-upload-btn.btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: var(--text-color) !important;
        }

        .fixed-upload-btn.btn-primary:hover {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* ========== TRANSISI WARNA ========== */
        *,
        .btn,
        .btn-primary,
        .topbar,
        .sidebar,
        .sidebar a {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Search Bar Styling */
.search-bar {
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    z-index: 99;
}

.search-bar .input-group {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.search-bar .form-control {
    border: none;
    padding: 10px 16px;
    font-size: 0.95rem;
}

.search-bar .btn {
    padding: 10px 20px;
    font-weight: 500;
}
    </style>
</head>

<body>

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center px-3 py-2"
        style="background: var(--topbar-bg); color: var(--text-color); box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
        <!-- Brand / Logo -->
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <span class="fw-bold text-white fs-5">Bakti Nusantara 666</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Ganti Tema -->
            <div class="dropdown me-1">
                <button class="btn btn-sm btn-outline-light rounded-circle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" title="Ganti Tema">
                    <i class="fas fa-palette"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item theme-option" data-theme="blue" href="#"><i class="fas fa-circle text-primary me-2"></i> Biru</a></li>
                    <li><a class="dropdown-item theme-option" data-theme="green" href="#"><i class="fas fa-circle text-success me-2"></i> Hijau</a></li>
                    <li><a class="dropdown-item theme-option" data-theme="purple" href="#"><i class="fas fa-circle text-purple me-2"></i> Ungu</a></li>
                    <li><a class="dropdown-item theme-option" data-theme="dark" href="#"><i class="fas fa-moon me-2"></i> Gelap</a></li>
                    <li><a class="dropdown-item theme-option" data-theme="light" href="#"><i class="fas fa-sun me-2"></i> Terang</a></li>
                </ul>
            </div>

            <!-- Dropdown Profil & Menu -->
            @if(Auth::check())
                <div class="dropdown">
                    <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_photo
                            ? asset('storage/' . Auth::user()->profile_photo)
                            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0d47a1&color=fff&size=64' }}"
                            alt="Foto Profil"
                            class="rounded-circle"
                            style="width: 36px; height: 36px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 240px;">
                        <!-- Info Pengguna -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.show', Auth::id()) }}">
                                <i class="fas fa-user me-3 text-primary"></i>
                                <div>
                                    <strong>{{ Auth::user()->name }}</strong><br>
                                    <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>

                        <!-- Menu Utama -->
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-2"></i> Beranda
                            </a>
                        </li>

                        @if(Auth::user()->isAdmin())
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/admin">
                                    <i class="fas fa-cog me-2"></i> Admin Panel
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isGuru() || Auth::user()->isAdmin())
                            <li>
                                <a class="dropdown-item" href="/guru">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> Guru Dashboard
                                </a>
                            </li>
                        @endif

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            @else
                <!-- Jika belum login -->
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Login</a>
            @endif
        </div>
    </div>

<!-- Search Bar (Desktop Only) - Live Search -->
<div class="search-bar d-none d-lg-block bg-white border-bottom py-3">
    <div class="container d-flex justify-content-center">
        <div class="position-relative" style="max-width: 500px; width: 100%;">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="desktopSearchInput" class="form-control"
                       placeholder="Cari pengguna..."
                       autocomplete="off">
                <button class="btn btn-primary" type="button" disabled>
                    <i class="fas fa-spinner fa-spin d-none" id="desktopSearchSpinner"></i>
                    Cari
                </button>
            </div>
            <!-- Dropdown hasil pencarian -->
            <div id="desktopSearchResults" class="position-absolute bg-white shadow rounded mt-1 w-100"
                 style="z-index: 1000; display: none; max-height: 400px; overflow-y: auto;"></div>
        </div>
    </div>
</div>

    <!-- Sidebar -->
    @include('particial.sidebar')

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Konten Utama -->
    @include('particial.content')

    <!-- Floating Upload Button -->
    <!-- ✅ Letakkan di sini agar selalu muncul di semua halaman -->
    <a href="{{ route('upload.store') }}" class="btn btn-primary rounded-circle shadow-lg fixed-upload-btn">
        <i class="fas fa-upload"></i>
    </a>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- ✅ SCRIPTS -->
    <!-- Bootstrap Bundle JS (Popper + Bootstrap JS) -->
    <!-- ✅ Perbaikan: Hapus spasi ekstra di URL -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const menuToggle = document.getElementById('menuToggle');

            // Optional: jika elemen tidak ada, jangan error fatal
            if (menuToggle && sidebar && overlay) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.add('open');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                    document.body.style.overflow = 'auto';
                });

                document.querySelectorAll('.sidebar a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 992) {
                            setTimeout(() => {
                                sidebar.classList.remove('open');
                                overlay.classList.remove('show');
                                document.body.style.overflow = 'auto';
                            }, 300);
                        }
                    });
                });
            }

            // Theme switcher
            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                console.log(`Theme changed to: ${theme}`);
            }

            // Ambil tema dari localStorage, atau gunakan 'blue' sebagai default
            const savedTheme = localStorage.getItem('theme') || 'blue';
            applyTheme(savedTheme);

            // Pasang event listener ke setiap opsi tema
            document.querySelectorAll('.theme-option').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    const theme = this.getAttribute('data-theme');
                    applyTheme(theme);
                });
            });
        });
    </script>
<!-- Bottom Navbar (Mobile Only) -->
<nav class="mobile-bottom-nav d-md-none">
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    <!-- ✅ Tambahkan ID 'search-tab' sesuai JS -->
    <a href="#" id="search-tab" class="nav-item">
        <i class="fas fa-search"></i>
        <span>Cari</span>
    </a>
</nav>

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

<!-- Tambahkan CSS & JS Eksternal -->
<link rel="stylesheet" href="{{ asset('css/mobile-bottom-nav.css') }}">
<script src="{{ asset('javascript/mobile-bottom-nav.js') }}" defer></script>
@stack('scripts')

</body>

</html>