
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
