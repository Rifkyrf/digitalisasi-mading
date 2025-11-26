<!-- Tambahkan div pembungkus dengan id="modalContent" -->
<div id="modalContent">
    <div class="container-fluid px-3">
        <!-- Header Pengunggah -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ $work->user->profile_photo_url }}" alt="Foto Profil" class="rounded-circle"
                style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #0d6efd;">

            <div>
                <strong>{{ $work->user->name }}</strong>
                <p class="mb-0 text-muted small">
                    {{ $work->user->nis ?? 'NIS tidak tersedia' }} •
                    <span class="badge bg-info">{{ ucfirst($work->user->hakguna->name ?? 'siswa') }}</span>
                </p>
            </div>
        </div>

        <!-- Judul -->
        <h5 class="fw-bold mb-2">{{ $work->title }}</h5>

        <!-- Jenis & Tanggal -->
        @php
            $typeLabels = [
                'karya' => 'Karya Siswa',
                'mading' => 'Mading Digital',
                'mingguan' => 'weekly',
                'harian' => 'daily',
                'prestasi' => 'prestasi',
                'opini' => 'opini',
                'event' => 'event',
            ];
        @endphp
        <small class="text-muted d-block mb-3">
            {{ $typeLabels[$work->type] ?? 'Konten' }} •
            {{ \Carbon\Carbon::parse($work->created_at)->format('d M Y, H:i') }}
        </small>

        <!-- Preview File -->
        @php
            $ext = strtolower($work->file_type ?? pathinfo($work->file_path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isVideo = in_array($ext, ['mp4', 'webm', 'mov', 'avi']);
        @endphp

        <div class="text-center mb-3">
            @if($isImage)
                <img src="{{ asset('storage/' . $work->file_path) }}" class="img-fluid rounded shadow-sm"
                    style="max-height: 350px; object-fit: cover;">
            @elseif($isVideo)
                <video controls class="w-100 rounded shadow-sm" style="max-height: 350px;">
                    <source src="{{ asset('storage/' . $work->file_path) }}" type="video/{{ $ext }}">
                    Browser tidak mendukung video.
                </video>
            @else
                <img src="{{ $work->thumbnail_path
                ? asset('storage/' . $work->thumbnail_path)
                : asset('storage/icons/file.png') }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
            @endif
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
            <small class="text-muted">Deskripsi:</small>
            <div class="bg-light p-3 rounded" style="white-space: pre-wrap; font-size: 0.95rem;">
                {!! preg_replace(
                    '/(https?:\/\/[^\s]+)/',
                    '<a href="$1" target="_blank" class="text-primary text-decoration-underline">$1</a>',
                    htmlspecialchars($work->description ?? 'Tidak ada deskripsi.')
                ) !!}
            </div>
        </div>

        <!-- Like & Komentar Interaktif -->
        <div class="mb-3">
            <!-- Like Button -->
            @if(auth()->check())
                <form method="POST" action="{{ route('likes.toggle', $work) }}" class="d-inline like-form" data-work-id="{{ $work->id }}">
                    @csrf
                    @method('POST')
                    <button type="submit"
                        class="btn {{ $userLiked ? 'btn-danger' : 'btn-outline-primary' }} btn-sm d-flex align-items-center gap-1">
                        <i class="fas fa-heart"></i>
                        <span class="like-count">{{ $likesCount }}</span> Like
                    </button>
                </form>
            @else
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" disabled>
                    <i class="fas fa-heart"></i> {{ $likesCount }} Like
                </button>
            @endif

            <!-- Form Komentar -->
            @if(auth()->check())
                <form class="comment-form mt-2" action="{{ route('comments.store') }}" data-work-id="{{ $work->id }}">
                    @csrf
                    <input type="hidden" name="work_id" value="{{ $work->id }}">
                    <div class="input-group">
                        <input type="text" name="content" class="form-control form-control-sm" placeholder="Tulis komentar..."
                            maxlength="500" required>
                        <button class="btn btn-primary btn-sm" type="submit">Kirim</button>
                    </div>
                    <div class="text-danger mt-1 comment-error" style="display:none;"></div>
                </form>

                <!-- Daftar Komentar -->
                <div class="mt-2" id="commentList">
                    @foreach($comments as $comment)
                        <div class="small mb-1" data-comment-id="{{ $comment->id }}">
                            <strong>{{ $comment->user->name }}:</strong>
                            {{ $comment->content }}
                            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                            @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin()))
                                <div class="btn-group btn-group-sm ms-2">
                                    <button class="btn btn-xs btn-outline-warning edit-comment-btn"
                                            data-comment-id="{{ $comment->id }}"
                                            data-comment-content="{{ $comment->content }}"
                                            title="Edit komentar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger delete-comment-btn"
                                            data-comment-id="{{ $comment->id }}"
                                            title="Hapus komentar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <small class="text-muted mt-1 d-block">Login untuk like & komentar.</small>
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-center gap-2">
            @if($isImage || $isVideo)
                <a href="{{ asset('storage/' . $work->file_path) }}" class="btn btn-success btn-sm" target="_blank" download>
                    <i class="fas fa-download me-1"></i> Unduh
                </a>
            @else
                <a href="{{ asset('storage/' . $work->file_path) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="fas fa-file me-1"></i> Buka File
                </a>
            @endif

            @if(auth()->check() && (auth()->id() === $work->user_id || auth()->user()->isAdmin() || auth()->user()->isGuru()))
                <a href="{{ route('works.edit', $work->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <form method="POST" action="{{ route('work.destroy', $work->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </form>
            @endif
        </div>

        <!-- 💡 ALTERNATIF BAGIKAN LINK -->
        <div class="text-center mt-4">
            <p class="small text-muted mb-1">Bagikan link karya ini:</p>
            <div class="input-group" style="max-width: 300px; margin: 0 auto;">
                <input type="text" class="form-control form-control-sm text-truncate"
                       value="{{ rtrim(request()->fullUrl(), '/modal') }}" readonly>
                <button class="btn btn-outline-secondary btn-sm"
                        onclick="this.previousElementSibling.select(); document.execCommand('copy'); alert('✅ Link disalin!')">
                    Salin
                </button>
            </div>
        </div>
    </div>

    <style>
        .small {
            font-size: 0.875rem;
        }

        .btn-xs {
            padding: 0.1rem 0.4rem;
            font-size: 0.75rem;
        }
    </style>
</div>