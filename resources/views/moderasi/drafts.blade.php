@extends('layouts.app')

@section('title', 'Moderasi Karya')

@section('content')
<div class="container">
    <h2 class="mb-4">Moderasi Karya</h2>

    <!-- Form Pencarian -->
    <form method="GET" action="{{ route('moderasi.drafts') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul, penulis, atau tipe..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Cari</button>
            @if(request('search'))
                <a href="{{ route('moderasi.drafts') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <!-- Tampilkan Notifikasi Baru di sini -->
    @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isGuru()))
        @php
            $newDraftNotifications = Auth::user()->unreadNotifications()->where('type', 'App\Notifications\DraftSubmitted')->get();
        @endphp
        @if($newDraftNotifications->count() > 0)
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <strong>Ada {{ $newDraftNotifications->count() }} draft baru!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endif

    @if($works->isEmpty())
        <div class="alert alert-info text-center">Tidak ada karya yang ditemukan.</div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40%;">Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($works as $work)
                    <tr>
                        <!-- Judul & Thumbnail -->
                        <td>
                            <div class="d-flex align-items-start">
                                @if($work->thumbnail_path)
                                    <img src="{{ asset('storage/' . $work->thumbnail_path) }}"
                                         alt="Thumbnail"
                                         class="rounded me-3"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-normal">{{ Str::limit($work->title, 50) }}</h6>
                                    <small class="text-muted d-block">
                                        {{ Str::limit($work->description, 80) }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <!-- Penulis -->
                        <td>
                            <span class="fw-medium">{{ $work->user->name }}</span><br>
                            <small class="text-muted">{{ $work->user->role }}</small>
                        </td>

                        <!-- Kategori -->
                        <td>
                            <span class="badge bg-secondary">{{ $work->type_label }}</span>
                        </td>

                        <!-- Status -->
                        <td>
                            @if($work->status === 'draft')
                                <span class="badge bg-warning text-dark">Draft</span>
                            @elseif($work->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($work->status) }}</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('moderator.show', $work) }}"
                                   class="btn btn-outline-primary"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($work->status === 'draft')
                                    <form action="{{ route('moderasi.publish', $work) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-success"
                                                title="Publish"
                                                onclick="return confirm('Publikasikan artikel ini?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @elseif($work->status === 'published')
                                    <form action="{{ route('moderasi.unpublish', $work) }}" method="POST" class="d-inline"> <!-- Ganti ke route unpublish -->
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-warning"
                                                title="Unpublish"
                                                onclick="return confirm('Batalkan publikasi artikel ini?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $works->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .table th,
    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .btn i {
        font-size: 0.9em;
    }

    @media (max-width: 576px) {
        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem;
            background-color: #fff;
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border: none;
        }

        .table tbody td:before {
            content: attr(data-label);
            font-weight: bold;
            min-width: 100px;
        }
    }
</style>
@endpush