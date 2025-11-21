<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Bakti Nusantara 666 - Mading & Karya Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Css link --}}
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
@include('particial.navbar')


    <!-- Hero Section dengan Foto Sekolah -->
    <section class="sekolah-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6">
                    <h1 class="hero-title">SMK Bakti Nusantara 666</h1>
                    <p class="hero-description">
                        Sekolah Menengah Kejuruan yang berkomitmen untuk mencetak generasi unggul,
                        kreatif, dan berkarakter. Dengan berbagai program keahlian yang relevan
                        dengan kebutuhan industri, kami siap membekali siswa dengan keterampilan
                        yang dibutuhkan di era digital.
                    </p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">Siswa</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Guru</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">10+</span>
                            <span class="stat-label">Jurusan</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container">
                        @if(file_exists(public_path('images/sekolah_hero.png')))
                            <img src="{{ asset('images/sekolah_hero.png') }}" alt="SMK Bakti Nusantara 666"
                                class="hero-image">
                        @elseif(file_exists(public_path('images/sekolah-hero.jpg')))
                            <img src="{{ asset('images/sekolah-hero.jpg') }}" alt="SMK Bakti Nusantara 666"
                                class="hero-image">
                        @else
                            <div class="placeholder-hero">
                                <i class="fas fa-school fa-5x mb-3"></i>
                                <p>Foto SMK Bakti Nusantara 666</p>
                                <small class="text-muted">Letakkan foto sekolah di: public/images/sekolah_hero.png</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== ARTIKEL POPULER ========== -->
    @if($popularWorks->count() > 0)
    <section class="popular-articles-section">
        <div class="container">
            <div class="section-header text-center">
                <h2><i class="fas fa-star me-2"></i>Artikel Populer</h2>
                <p class="section-subtitle">Karya terbaik dan paling banyak disukai oleh komunitas SMK Bakti Nusantara 666</p>
            </div>

            <div class="row g-4">
                @foreach($popularWorks as $work)
                <div class="col-md-4 col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-image-container">
                            <img src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}" class="card-img-top">
                            <div class="card-type-badge">{{ strtoupper($work->type_label) }}</div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" title="{{ $work->title }}">{{ Str::limit($work->title, 40) }}</h5>
                            <p class="card-text">{{ Str::limit($work->description, 60) }}</p>
                            <div class="card-meta">
                                <small class="d-block text-muted mb-1">
                                    <i class="fas fa-user me-1"></i> Oleh: <strong>{{ $work->user->name }}</strong>
                                </small>
                                <small class="text-secondary">
                                    <i class="fas fa-clock me-1"></i> {{ $work->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('work.show', $work->id) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('popular') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-ellipsis-h me-1"></i> Lihat Semua Artikel Populer
                </a>
            </div>
        </div>
    </section>
    @endif
    <!-- ========== END ARTIKEL POPULER ========== -->

    <!-- Filter Buttons Modern -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-container d-flex flex-wrap justify-content-start gap-2">
                <a href="?type=all"
                    class="filter-btn {{ request('type') == 'all' || !request('type') ? 'active' : '' }}">
                    <i class="fas fa-th-large me-2"></i>Semua
                </a>
                <a href="?type=mading" class="filter-btn {{ request('type') == 'mading' ? 'active' : '' }}">
                    <i class="fas fa-newspaper me-2"></i>Mading Digital
                </a>
                <a href="?type=karya" class="filter-btn {{ request('type') == 'karya' ? 'active' : '' }}">
                    <i class="fas fa-palette me-2"></i>Karya Siswa
                </a>
                <a href="?type=mingguan" class="filter-btn {{ request('type') == 'mingguan' ? 'active' : '' }}">
                    <i class="fas fa-calendar-week me-2"></i>Konten Mingguan
                </a>
                <a href="?type=harian" class="filter-btn {{ request('type') == 'harian' ? 'active' : '' }}">
                    <i class="fas fa-calendar-day me-2"></i>Konten Harian
                </a>
                <a href="?type=prestasi" class="filter-btn {{ request('type') == 'prestasi' ? 'active' : '' }}">
                    <i class="fas fa-trophy me-2"></i>Prestasi Siswa
                </a>
                <a href="?type=opini" class="filter-btn {{ request('type') == 'opini' ? 'active' : '' }}">
                    <i class="fas fa-comment me-2"></i>Opini
                </a>
                <a href="?type=event" class="filter-btn {{ request('type') == 'event' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-2"></i>Event
                </a>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <h2>
                    @if(request('type') == 'mading')
                        <i class="fas fa-newspaper me-2"></i>Mading Digital
                    @elseif(request('type') == 'karya')
                        <i class="fas fa-palette me-2"></i>Karya Siswa
                    @elseif(request('type') == 'mingguan')
                        <i class="fas fa-calendar-week me-2"></i>Konten Mingguan
                    @elseif(request('type') == 'harian')
                        <i class="fas fa-calendar-day me-2"></i>Konten Harian
                    @elseif(request('type') == 'prestasi')
                        <i class="fas fa-trophy me-2"></i>Prestasi Siswa
                    @elseif(request('type') == 'opini')
                        <i class="fas fa-comment me-2"></i>Opini
                    @elseif(request('type') == 'event')
                        <i class="fas fa-calendar-alt me-2"></i>Event
                    @else
                        <i class="fas fa-th-large me-2"></i>Semua Konten
                    @endif
                </h2>
                <p class="section-subtitle">Jelajahi berbagai karya dan informasi dari siswa SMK Bakti Nusantara 666</p>
            </div>

            <div class="row g-4">
                @forelse($works as $index => $work)
                    @php
                        $show = false;
                        $currentType = request('type');

                        if (!$currentType || $currentType === 'all') {
                            $show = true;
                        } elseif ($currentType === 'karya' && $work->type === 'karya') {
                            $show = true;
                        } elseif ($currentType === 'mading' && $work->type === 'mading') {
                            $show = true;
                        } elseif ($currentType === 'harian' && $work->type === 'harian') {
                            $show = true;
                        } elseif ($currentType === 'mingguan' && $work->type === 'mingguan') {
                            $show = true;
                        } elseif ($currentType === 'prestasi' && $work->type === 'prestasi') {
                            $show = true;
                        } elseif ($currentType === 'opini' && $work->type === 'opini') {
                            $show = true;
                        } elseif ($currentType === 'event' && $work->type === 'event') {
                            $show = true;
                        }
                    @endphp

                    @if($show)
                        <div class="col-md-4 col-12 work-card" style="animation-delay: {{ $index * 0.1 }}s;">
                            <div class="card h-100">
                                <div class="card-image-container">
                                    <img src="{{ $work->thumbnail_url }}"
                                        class="card-img-top" alt="{{ $work->title }}">
                                    <div class="card-type-badge">{{ strtoupper($work->type_label) }}</div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" title="{{ $work->title }}">
                                        {{ Str::limit($work->title, 40) }}
                                    </h5>
                                    <p class="card-text">
                                        {{ Str::limit($work->description, 60) }}
                                    </p>
                                    <div class="card-meta">
                                        <small class="d-block text-muted mb-1">
                                            <i class="fas fa-user me-1"></i>Oleh: <strong>{{ $work->user->name }}</strong>
                                        </small>
                                        <small class="text-secondary">
                                            <i class="fas fa-file me-1"></i>{{ strtoupper($work->file_type) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('work.show', $work->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Tidak ada konten yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($works->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if($works->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $works->previousPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
                                        rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach($works->links()->elements[0] as $page => $url)
                                @if($page == $works->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $url }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if($works->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $works->nextPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
                                        rel="next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </section>

    @include('particial.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('javascript/landing.js') }}"></script>
</body>

</html>