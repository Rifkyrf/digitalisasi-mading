@extends('layouts.app')

@section('title', $work->title . ' - Preview Moderasi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <!-- Card Utama -->
            <div class="card shadow-sm border">
                @if($work->thumbnail_path)
                    <img src="{{ asset('storage/' . $work->thumbnail_path) }}" class="card-img-top" alt="{{ $work->title }}" style="height: 250px; object-fit: cover;">
                @endif

                <div class="card-body">
                    <!-- Judul -->
                    <h4 class="card-title fw-bold">{{ $work->title }}</h4>

                    <!-- Info Status & Penulis -->
                    <p class="text-muted small mb-3">
                        Oleh: <strong>{{ $work->user->name }}</strong> |
                        Status: <span class="badge bg-warning text-dark">{{ ucfirst($work->status) }}</span> |
                        Tipe: <span class="badge bg-secondary">{{ $work->type_label }}</span>
                    </p>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <p>{!! nl2br(e($work->description)) !!}</p>
                    </div>

                    <!-- Preview File -->
                    @if($work->file_path)
                        <div class="mb-3">
                            <h6><i class="fas fa-paperclip"></i> Lampiran:</h6>
                            @php
                                $extension = pathinfo($work->file_path, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset('storage/' . $work->file_path) }}" class="img-fluid rounded" style="max-height: 300px; object-fit: contain;" alt="File">
                            @elseif($extension === 'pdf')
                                <a href="{{ asset('storage/' . $work->file_path) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Buka PDF
                                </a>
                            @elseif(in_array($extension, ['mp4', 'mov', 'avi']))
                                <video controls class="w-100 rounded" style="max-height: 400px;">
                                    <source src="{{ asset('storage/' . $work->file_path) }}" type="video/{{ $extension }}">
                                    Browser tidak mendukung video.
                                </video>
                            @elseif(in_array($extension, ['mp3', 'wav']))
                                <audio controls class="w-100">
                                    <source src="{{ asset('storage/' . $work->file_path) }}" type="audio/{{ $extension }}">
                                    Browser tidak mendukung audio.
                                </audio>
                            @else
                                <a href="{{ asset('storage/' . $work->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download"></i> Unduh {{ strtoupper($extension) }}
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="text-muted"><em>Tidak ada file lampiran.</em></p>
                    @endif

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('moderasi.drafts') }}" class="btn btn-outline-secondary btn-sm">
                            ← Kembali
                        </a>
                        <form action="{{ route('moderasi.publish', $work) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Publikasikan artikel ini?')">
                                ✅ Publish
                            </button>
                        </form>
                        <form action="{{ route('work.destroy', $work) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus artikel ini?')">
                                ❌ Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection