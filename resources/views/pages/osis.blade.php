<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSIS - Bakti Nusantara 666</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Css link --}}
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 80px;
            background-color: #f9f9f9;
        }

        .navbar {
            background-color: #1e3a8a;
        }

        /* 7 Inti OSIS - Zigzag */
        .zigzag-card {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .zigzag-card:nth-child(even) {
            flex-direction: row-reverse;
            text-align: right;
        }

        .zigzag-card img {
            width: 180px;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #3b82f6;
        }

        .zigzag-card .info h4 {
            margin-bottom: 0.3rem;
            color: #1e3a8a;
            font-size: 1.3rem;
        }

        .zigzag-card .info p {
            margin: 0;
            color: #475569;
            font-weight: 600;
        }

        /* Sekbid Cards */
        .sekbid-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .sekbid-card .card-body h5 {
            font-size: 1.1rem;
            color: #1e3a8a;
        }

        .sekbid-card .card-body p {
            color: #475569;
            font-size: 0.95rem;
        }

        .footer {
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
            background: #f1f5f9;
            color: #64748b;
        }

        /* Atur tinggi navbar secara eksplisit */
        .navbar {
            height: 70px;
            /* Tinggi navbar diperbesar dari default */
        }

        /* Pastikan padding-top body sama dengan tinggi navbar */
        body {
            padding-top: 70px;
        }

        /* Perbesar ukuran logo dan teks di navbar */
        .navbar-brand {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .navbar-brand img {
            height: 35px;
            margin-right: 8px;
        }

        /* Perbesar tombol di navbar */
        .btn.btn-outline-light.btn-sm {
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
            border-radius: 0.375rem;
        }

        /* Mobile: Atur ukuran font dan padding pada tombol navbar agar tidak terlalu besar */
        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand img {
                height: 30px;
            }

            .btn.btn-outline-light.btn-sm {
                font-size: 0.85rem;
                padding: 0.4rem 0.7rem;
            }

            .filter-btn {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
                margin: 0.25rem 0.25rem;
            }
        }

        /* Mobile: Pastikan filter button tidak terlalu lebar dan bisa scroll horizontal jika perlu */
        @media (max-width: 768px) {
            .filter-container {
                overflow-x: auto;
                white-space: nowrap;
                padding: 0.5rem 0;
                scrollbar-width: thin;
                scrollbar-color: #ccc #f8f9fa;
            }

            .filter-container::-webkit-scrollbar {
                height: 6px;
            }

            .filter-container::-webkit-scrollbar-thumb {
                background-color: #ccc;
                border-radius: 10px;
            }

            .filter-container::-webkit-scrollbar-track {
                background: #f8f9fa;
            }
        }

        .navbar-brand img {
            height: 35px;
            margin-right: 8px;
            /* Tambahkan properti ini untuk memastikan tidak ada background putih jika gambar logo adalah PNG transparan */
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
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

    <div class="container mt-4">

        <!-- 🔍 Form Pencarian Angkatan -->
        <div class="text-center mb-4">
            <form method="GET" class="d-inline">
                <label for="angkatan" class="form-label fw-bold">Tampilkan Struktur OSIS untuk Angkatan:</label><br>
                <div class="input-group" style="max-width: 300px; margin: 0 auto;">
                    <input type="text" name="angkatan" id="angkatan" class="form-control"
                        placeholder="Contoh: 2024/2025" value="{{ request('angkatan') }}" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>

            <!-- Opsional: Tampilkan daftar angkatan yang tersedia -->
            @if($angkatanList->isNotEmpty())
                <div class="mt-2">
                    <small class="text-muted">Angkatan saat ini:
                        @foreach($angkatanList as $angk)
                            <a href="{{ route('osis.index', ['angkatan' => $angk]) }}" class="text-decoration-none">
                                {{ $angk }}
                            </a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </small>
                </div>
            @endif
        </div>

        <!-- 7 Inti OSIS -->
        <h2 class="text-center mb-5">7 Inti Struktur OSIS • Angkatan {{ $angkatanAktif }}</h2>
        @if($intiOsis->isEmpty())
            <div class="alert alert-info text-center">Belum ada pengurus inti untuk angkatan {{ $angkatanAktif }}.</div>
        @else
            @foreach($intiOsis as $member)
                <div class="zigzag-card">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                    <div class="info">
                        <h4>{{ $member->name }}</h4>
                        <p>{{ ucfirst($member->role) }} • Angkatan {{ $member->angkatan }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        <hr class="my-5">

        <!-- Sekbid -->
        <h3 class="text-center mb-4">Sekretariat Bidang (Sekbid) • Angkatan {{ $angkatanAktif }}</h3>
        @if($sekbid->isEmpty())
            <div class="alert alert-info text-center">Belum ada anggota sekbid untuk angkatan {{ $angkatanAktif }}.</div>
        @else
            <div class="row g-4">
                @foreach($sekbid as $member)
                    <div class="col-md-4 col-sm-6">
                        <div class="card sekbid-card shadow-sm">
                            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                            <div class="card-body text-center">
                                <h5>{{ $member->name }}</h5>
                                <p class="mb-0">{{ ucfirst($member->role) }} • {{ $member->nama_sekbid ?? 'Sekbid' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <!-- Footer -->
    @include('particial.footer')

</body>

</html>