{{-- @extends('layouts.guest')


hanya untuk guest yang tidak jadi dipakai
@section('title', 'Detail Karya')

@section('content')
<div class="container-fluid py-4" style="background-color: #f0f2f5; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Card Utama -->
            <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                <!-- Header -->
                <div class="bg-primary text-white px-4 py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Detail Karya
                    </h4>
                    <p class="mb-0 opacity-75" style="font-size: 0.9rem;">Lihat dan unduh karya siswa</p>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <!-- Informasi Pengunggah (Instagram Style) -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <!-- Foto Profil -->
                        <img src="{{ $work->user->profile_photo_url }}"
                             alt="Foto Profil"
                             class="rounded-circle"
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #0d47a1;">

                        <!-- Nama & Info -->
                        <div>
                            <strong>{{ $work->user->name }}</strong>
                            <p class="mb-0 text-muted small">
                                {{ $work->user->nis }} •
                                <span class="badge bg-info">{{ ucfirst($work->user->role) }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Judul -->
                    <h5 class="fw-bold mb-3">{{ $work->title }}</h5>

                    <!-- Jenis Konten & Tanggal -->
                    <div class="mb-4">
                        @php
                            $typeLabels = [
                                'karya' => 'Karya Siswa',
                                'mading' => 'Mading Digital',
                                'berita' => 'Berita Sekolah',
                                'pengumuman' => 'Pengumuman',
                                'pelajaran' => 'Materi Pelajaran'
                            ];
                        @endphp
                        <small class="text-muted">
                            {{ $typeLabels[$work->type] ?? 'Konten' }} •
                            {{ \Carbon\Carbon::parse($work->created_at)->format('d M Y, H:i') }}
                        </small>
                    </div>

                    <!-- Preview Konten (Video, Gambar, atau Placeholder) -->
                    <div class="text-center mb-4">
                        @php
                            $extension = strtolower($work->file_type);
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'avi']);
                        @endphp

                        @if($isImage)
                            <img src="{{ asset('storage/' . $work->file_path) }}"
                                 alt="Gambar Karya"
                                 class="img-fluid rounded"
                                 style="max-height: 400px; object-fit: cover; width: auto;">

                        @elseif($isVideo)
                            <video controls class="w-100 rounded" style="max-height: 400px;">
                                <source src="{{ asset('storage/' . $work->file_path) }}" type="video/{{ $extension }}">
                                Browser Anda tidak mendukung pemutar video.
                            </video>

                        @else
                            <img src="{{ $work->thumbnail_path
                                ? asset('storage/' . $work->thumbnail_path)
                                : asset('storage/icons/file.png') }}"
                                 alt="Thumbnail"
                                 class="img-fluid rounded"
                                 style="max-height: 300px; object-fit: cover; width: auto;">
                        @endif
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">
                            {!!
                                preg_replace(
                                    '/(https?:\/\/[^\s]+)/',
                                    '<a href="$1" target="_blank" class="text-primary text-decoration-underline">$1</a>',
                                    htmlspecialchars($work->description ?? 'Tidak ada deskripsi.')
                                )
                            !!}
                        </div>
                    </div>

                    <!-- Interaksi: Like & Komentar -->
                    <div class="row mb-4">
                        <div class="col-6">
                            @if(Auth::check())
                                <form method="POST" action="{{ route('likes.toggle', $work) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $userLiked ? 'btn-danger' : 'btn-outline-primary' }} btn-sm d-flex align-items-center gap-2">
                                        <i class="fas fa-heart"></i>
                                        <span>{{ $work->likes->count() }} Like</span>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" disabled>
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $work->likes->count() }} Like</span>
                                </button>
                            @endif
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" disabled>
                                <i class="fas fa-comment"></i>
                                <span>{{ $comments->count() }} Komentar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Komentar -->
                    <div class="mt-4">
                        <h6>Komentar (<span id="commentCount">{{ $comments->count() }}</span>):</h6>

                        <div id="commentList">
                            @forelse($comments as $comment)
                                <div class="mb-3 p-2 bg-light rounded" data-comment-id="{{ $comment->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>
                                            <p class="mb-0">{{ $comment->content }}</p>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->isAdmin()))
                                            <button class="btn btn-sm btn-outline-danger delete-comment-btn"
                                                    data-comment-id="{{ $comment->id }}"
                                                    title="Hapus komentar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted"><em>Belum ada komentar.</em></p>
                            @endforelse
                        </div>

                        <!-- Form Komentar -->
                        @if(Auth::check())
                            <form id="commentForm" class="mt-3">
                                @csrf
                                <input type="hidden" name="work_id" value="{{ $work->id }}">
                                <div class="input-group">
                                    <input type="text" name="content" class="form-control" placeholder="Tulis komentar..." maxlength="1000" required>
                                    <button class="btn btn-primary" type="submit">Kirim</button>
                                </div>
                                <div class="text-danger mt-2" id="commentError" style="display:none;"></div>
                            </form>
                        @else
                            <p class="text-muted mt-2"><small>Login untuk berkomentar.</small></p>
                        @endif
                    </div>

                    <!-- Tombol Bagikan -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-info btn-sm" onclick="copyLink()">
                            <i class="fas fa-share-alt me-1"></i> Bagikan Link
                        </button>
                    </div>

                    <!-- Aksi -->
                    <div class="d-flex justify-content-between mt-5">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>

                        @if(Auth::check() && ($work->user_id == Auth::id() || Auth::user()->isAdmin()))
                            <a href="{{ route('work.edit', $work->id) }}" class="btn btn-warning px-4">
                                <i class="fas fa-edit me-2"></i> Edit
                            </a>
                        @endif

                        <!-- Tombol Unduh / Buka File -->
                        @if($isImage || $isVideo)
                            <a href="{{ asset('storage/' . $work->file_path) }}"
                               class="btn btn-success px-4"
                               target="_blank">
                                <i class="fas fa-download me-2"></i> Unduh
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $work->file_path) }}"
                               class="btn btn-success px-4"
                               target="_blank">
                                <i class="fas fa-cloud-download-alt me-2"></i> Buka File
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 text-muted" style="font-size: 0.9rem;">
                © 2025 Bakti Nusantara 666 • Portal Karya Siswa
            </div>
        </div>
    </div>
</div>

<!-- Script Bagikan Link -->
<script>
function copyLink() {
    const url = window.location.href;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link berhasil disalin!\nBagikan ke temanmu 😊');
        }).catch(() => fallbackCopy(url));
    } else {
        fallbackCopy(url);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('Link disalin!');
}
</script>

<!-- Style Tambahan -->
<style>
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .card {
        border-radius: 16px;
    }
    .bg-light {
        border-radius: 8px;
    }
    video {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
    }
    @media (max-width: 576px) {
        .text-center img, video {
            width: 100% !important;
            height: auto !important;
        }
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- KIRIM KOMENTAR ---
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(commentForm);
            const errorDiv = document.getElementById('commentError');
            const commentList = document.getElementById('commentList');
            const commentCount = document.getElementById('commentCount');

            errorDiv.style.display = 'none';
            errorDiv.textContent = '';

            fetch("{{ route('comments.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    commentForm.reset();

                    const newComment = document.createElement('div');
                    newComment.className = 'mb-3 p-2 bg-light rounded';
                    newComment.setAttribute('data-comment-id', data.comment.id || 'temp');
                    newComment.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${data.comment.user_name}</strong>
                                <p class="mb-0">${data.comment.content}</p>
                                <small class="text-muted">${data.comment.created_at_human}</small>
                            </div>
                        </div>
                    `;
                    commentList.appendChild(newComment);

                    if (commentCount) {
                        commentCount.textContent = parseInt(commentCount.textContent) + 1;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const msg = error.message || 'Gagal mengirim komentar.';
                errorDiv.textContent = msg;
                errorDiv.style.display = 'block';
            });
        });
    }

    // --- HAPUS KOMENTAR ---
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function () {
            const commentId = this.getAttribute('data-comment-id');
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            const commentCount = document.getElementById('commentCount');

            if (!confirm('Yakin ingin menghapus komentar ini?')) return;

            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && commentElement) {
                    commentElement.remove();
                    if (commentCount) {
                        let current = parseInt(commentCount.textContent) || 0;
                        commentCount.textContent = Math.max(0, current - 1);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus komentar: ' + (error.message || 'Coba lagi nanti.'));
            });
        });
    });
});
</script>
@endpush --}}