@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Karya Siswa</h2>
        <button type="button" class="btn btn-primary d-none d-md-inline" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload"></i> Upload Karya
        </button>
    </div>

    <div class="row">
        @foreach($works as $work)
        <div class="col-md-4 mb-4">
            <div class="card h-100 work-card"
                 style="cursor: pointer;"
                 data-bs-toggle="modal"
                 data-bs-target="#workModal"
                 data-work-id="{{ $work->id }}">
                <img src="{{ $work->thumbnail_path ? asset('storage/' . $work->thumbnail_path) : $work->icon }}"
                     class="card-img-top"
                     style="height: 180px; object-fit: cover;"
                     alt="{{ $work->title }}">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $work->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($work->description, 60) }}</p>
                    <small class="d-block text-muted mb-1">

    @if($work->user)
        <strong>{{ $work->user->name }}</strong>
    @else
        <span class="text-danger">[Pengguna Tidak Diketahui]</span>
    @endif
</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $works->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Floating Upload Button -->
<button type="button" class="btn btn-primary rounded-circle shadow-lg fixed-upload-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
    <i class="fas fa-upload"></i>
</button>

<!-- Modal: Detail Karya -->
<div class="modal fade" id="workModal" tabindex="-1" aria-labelledby="workModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Karya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="workModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Upload Karya -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Karya Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="uploadModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Karya -->
{{-- <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

{{-- CSS & JS Eksternal --}}
<link rel="stylesheet" href="{{ asset('css/karya-animasi.css') }}">
<script src="{{ asset('javascript/karya-animasi.js') }}" defer></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ambil CSRF token sekali saja
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ajaxHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
    };

    // === MODAL: DETAIL KARYA ===
    const workModal = document.getElementById('workModal');
    const workModalBody = document.getElementById('workModalBody');

    workModal.addEventListener('show.bs.modal', function (event) {
        const workId = event.relatedTarget.dataset.workId;
        workModalBody.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>`;

        fetch(`/works/${workId}/modal`, { headers: ajaxHeaders })
            .then(res => res.text())
            .then(html => {
                workModalBody.innerHTML = html;
                bindWorkModalEvents();
            })
            .catch(() => workModalBody.innerHTML = `<div class="alert alert-danger">Gagal memuat detail.</div>`);
    });

    function bindWorkModalEvents() {
        // Like
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const workId = form.dataset.workId;
                const btn = form.querySelector('button');
                const count = form.querySelector('.like-count');

                const res = await fetch(`/works/${workId}/like`, {
                    method: 'POST',
                    headers: ajaxHeaders
                });
                const data = await res.json();
                if (data.success) {
                    count.textContent = data.likes_count;
                    btn.classList.toggle('btn-danger', data.liked);
                    btn.classList.toggle('btn-outline-primary', !data.liked);
                }
            });
        });

        // Komentar
        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const workId = form.dataset.workId;
                const input = form.querySelector('input[name="content"]');
                const error = form.querySelector('.comment-error');
                const list = form.closest('.mb-3').querySelector('.comment-list');

                error.style.display = 'none';
                const content = input.value.trim();
                if (!content) return;

                const res = await fetch("{{ route('comments.store') }}", {
                    method: 'POST',
                    headers: {
                        ...ajaxHeaders,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ work_id: workId, content })
                });
                const data = await res.json();
                if (data.success) {
                    input.value = '';
                    if (list) {
                        const el = document.createElement('div');
                        el.className = 'small mb-1';
                        el.innerHTML = `<strong>${data.comment.user_name}:</strong> ${data.comment.content}`;
                        list.appendChild(el);
                    }
                } else {
                    error.textContent = data.message || 'Gagal mengirim komentar.';
                    error.style.display = 'block';
                }
            });
        });

        // Hapus komentar
        document.querySelectorAll('.delete-comment-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Hapus komentar ini?')) return;
                const id = btn.dataset.commentId;
                const res = await fetch(`/comments/${id}`, {
                    method: 'DELETE',
                    headers: ajaxHeaders
                });
                if (res.ok) {
                    btn.closest('[data-comment-id]').remove();
                } else {
                    alert('Gagal menghapus komentar.');
                }
            });
        });

        // Tombol Edit → buka modal edit
        document.querySelectorAll('.btn-edit-in-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const workId = btn.dataset.workId;
                fetch(`/work/${workId}/edit/form`, { headers: ajaxHeaders })
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('editModalBody').innerHTML = html;
                        bootstrap.Modal.getInstance(workModal).hide();
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
                    })
                    .catch(() => {
                        document.getElementById('editModalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat form edit.</div>';
                    });
            });
        });
    }

// === MODAL: UPLOAD ===
document.querySelectorAll('[data-bs-target="#uploadModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        fetch("{{ route('upload.form.modal') }}", { headers: ajaxHeaders })
            .then(r => r.text())
            .then(html => {
                document.getElementById('uploadModalBody').innerHTML = html;
                // Jalankan script yang ada di dalam HTML
                Array.from(document.getElementById('uploadModalBody').querySelectorAll('script')).forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => {
                        newScript.setAttribute(attr.name, attr.value);
                    });
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            })
            .catch(() => {
                document.getElementById('uploadModalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat form.</div>';
            });
    });
});
    // Reset konten modal saat ditutup
    [workModal, document.getElementById('uploadModal'), document.getElementById('editModal')].forEach(modal => {
        modal.addEventListener('hidden.bs.modal', () => {
            modal.querySelector('.modal-body').innerHTML = '';
        });
    });
});

// --- HAPUS KARYA ---
document.querySelectorAll('.btn-delete-work').forEach(btn => {
    btn.addEventListener('click', async () => {
        const workId = btn.dataset.workId;
        if (!confirm('Yakin ingin menghapus karya ini?')) return;

        try {
            const res = await fetch(`/work/${workId}`, {
                method: 'DELETE',
                headers: ajaxHeaders
            });

            if (res.ok) {
                bootstrap.Modal.getInstance(document.getElementById('workModal')).hide();
                location.reload();
            } else {
                alert('Gagal menghapus karya.');
            }
        } catch (err) {
            alert('Terjadi kesalahan.');
        }
    });
});
</script>
<style>
    /* Styling Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.page-item {
    border-radius: 0.375rem;
    overflow: hidden;
}

.page-link {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: #495057;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.page-link:hover {
    background-color: #e9ecef;
    color: #495057;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}


</style>
@endpush
@endsection