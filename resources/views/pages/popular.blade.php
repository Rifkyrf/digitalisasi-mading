<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel Populer - SMK Bakti Nusantara 666</title>
    <!-- Sertakan Bootstrap, Font Awesome, dan CSS kamu -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
</head>
<body>
    @include('particial.navbar') <!-- Pastikan partial navbar kamu ada -->

    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-fire me-2"></i> Artikel Populer</h2>
                <p class="section-subtitle">Karya-karya siswa yang paling banyak disukai dan dibaca.</p>
            </div>

            <div class="row g-4">
                @forelse($popularWorks as $work)
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
                                        <i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($work->created_at)->locale('id')->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('work.show', $work->id) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Belum ada karya yang populer.</p>
                    </div>
                @endforelse
            </div>

 <!-- Pagination -->
@if($popularWorks->hasPages())
<div class="d-flex justify-content-center mt-5">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <!-- Previous -->
            @if($popularWorks->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $popularWorks->previousPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
                       rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            <!-- Pages -->
            @foreach($popularWorks->links()->elements[0] as $page => $url)
                @if($page == $popularWorks->currentPage())
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

            <!-- Next -->
            @if($popularWorks->hasMorePages())
                <li class="page-item">
                    <a class="page-link"
                       href="{{ $popularWorks->nextPageUrl() }}{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}"
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