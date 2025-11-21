const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'CSRF_TOKEN_NOT_FOUND';

// --- Like System (Untuk Halaman Show dan Modal) ---
function handleLikeToggle(workId, buttonElement) {
    const countSelector = buttonElement.querySelector('.like-count');
    const originalHTML = buttonElement.innerHTML;

    if (csrfToken === 'CSRF_TOKEN_NOT_FOUND') {
        console.error('CSRF Token tidak ditemukan.');
        alert('Token keamanan tidak ditemukan. Harap refresh halaman.');
        return;
    }

    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/works/${workId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            buttonElement.classList.toggle('btn-danger', data.liked);
            buttonElement.classList.toggle('btn-outline-primary', !data.liked);
            if (countSelector) {
                countSelector.textContent = `${data.likes_count} Like`;
            }
        } else {
            throw new Error(data.message || 'Gagal memperbarui like.');
        }
    })
    .catch(error => {
        console.error('Like Error:', error);
        alert('Gagal memberi like: ' + error.message);
    })
    .finally(() => {
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalHTML;
    });
}

// --- Comment System (Insert ke DOM) (Untuk Halaman Show dan Modal) ---
function handleCommentSubmit(form) {
    const workId = form.getAttribute('data-work-id');
    const formData = new FormData(form);
    const errorDiv = form.querySelector('.comment-error');
    // Cari container komentar, bisa di modal atau halaman show
    const commentList = form.closest('.modal-content')?.querySelector('#commentList') || document.getElementById('commentList');

    if (!commentList) {
        console.error('Comment list container (id="commentList") not found.');
        return;
    }

    if (errorDiv) errorDiv.style.display = 'none';

    fetch("/comments", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();

            // 👇 INSERT KOMENTAR BARU KE ATAS DAFTAR
            const newComment = document.createElement('div');
            // Gunakan class dan struktur yang seragam antara modal dan show (sesuaikan dengan HTML kamu)
            // Contoh: untuk modal, struktur bisa beda. Kita buat yang seragam dan fleksibel.
            // Asumsikan struktur di show.blade.php adalah:
            // <div class="mb-3 p-2 bg-light rounded" data-comment-id="..."> <div class="d-flex justify-content-between align-items-start"> ... </div> </div>
            // Asumsikan struktur di modal adalah:
            // <div class="small mb-1" data-comment-id="..."> <strong>...</strong> ... </div>
            // Kita buat yang bisa menyesuaikan atau pilih satu format. Misalnya, gunakan format dari show.blade.php
            newComment.className = 'mb-3 p-2 bg-light rounded'; // Format dari show.blade.php
            newComment.setAttribute('data-comment-id', data.comment.id);
            newComment.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${data.comment.user_name}:</strong>
                        <p class="mb-0">${data.comment.content}</p>
                        <small class="text-muted">${data.comment.created_at_diff || 'Baru saja'}</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning edit-comment-btn" data-comment-id="${data.comment.id}" data-comment-content="${data.comment.content}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-comment-btn" data-comment-id="${data.comment.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            // Masukkan ke atas daftar komentar
            commentList.insertBefore(newComment, commentList.firstChild);

            // Update jumlah komentar di halaman show atau modal
            // Cari span jumlah komentar di halaman show atau modal
            const commentCountSpan = document.getElementById('commentCount') || form.closest('.modal-content')?.querySelector('#commentCount');
            if (commentCountSpan) {
                commentCountSpan.textContent = parseInt(commentCountSpan.textContent) + 1;
            }
        } else {
            throw new Error(data.message || 'Gagal mengirim komentar.');
        }
    })
    .catch(error => {
        console.error('Comment Error:', error);
        const msg = error.message || 'Gagal mengirim komentar.';
        if (errorDiv) {
            errorDiv.textContent = msg;
            errorDiv.style.display = 'block';
        } else {
            alert(msg);
        }
    });
}

// --- Edit & Delete Comment (Event Delegation) (Untuk Halaman Show dan Modal) ---
// --- Like Button (Event Delegation) (Untuk Halaman Show dan Modal) ---
// --- Comment Form Submit (Event Delegation) (Untuk Halaman Show dan Modal) ---
document.addEventListener('click', function(e) {
    // --- Like Button ---
    if (e.target.closest('.like-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.like-btn');
        const workId = btn.getAttribute('data-work-id');
        handleLikeToggle(workId, btn);
    }

    // --- Submit Comment Form ---
    else if (e.target.closest('.comment-form')) {
        e.preventDefault();
        const form = e.target.closest('.comment-form');
        handleCommentSubmit(form);
    }

    // --- Edit Komentar ---
    else if (e.target.closest('.edit-comment-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.edit-comment-btn');
        const commentId = btn.getAttribute('data-comment-id');
        const content = btn.getAttribute('data-comment-content');
        // Cari elemen komentar di halaman show atau modal
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        const userName = commentElement.querySelector('strong')?.textContent || 'User';
        const originalHTML = commentElement.innerHTML;

        if (!commentElement) return;

        commentElement.innerHTML = `
            <div class="d-flex justify-content-between align-items-start"> <!-- Struktur seragam -->
                <form class="edit-comment-form w-100" data-comment-id="${commentId}">
                    <textarea class="form-control mb-2" name="content" rows="2" required>${content}</textarea>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                        <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn">Batal</button>
                    </div>
                </form>
            </div>
        `;

        const editForm = commentElement.querySelector('.edit-comment-form');
        if (editForm) {
            editForm.addEventListener('submit', function(e2) {
                e2.preventDefault();
                const newContent = editForm.querySelector('textarea[name="content"]').value;

                fetch(`/comments/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: newContent })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        commentElement.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${userName}:</strong>
                                    <p class="mb-0">${data.content}</p>
                                    <small class="text-muted">${data.edited_at || 'Baru diedit'}</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning edit-comment-btn" data-comment-id="${commentId}" data-comment-content="${data.content}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-comment-btn" data-comment-id="${commentId}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        throw new Error(data.message || 'Gagal mengedit komentar.');
                    }
                })
                .catch(error => {
                    console.error('Edit Comment Error:', error);
                    alert('Gagal mengedit komentar. Coba lagi nanti.');
                    commentElement.innerHTML = originalHTML;
                });
            });
        }

        const cancelBtn = commentElement.querySelector('.cancel-edit-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => commentElement.innerHTML = originalHTML);
        }
    }

    // --- Delete Komentar ---
    else if (e.target.closest('.delete-comment-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.delete-comment-btn');
        const commentId = btn.getAttribute('data-comment-id');
        // Cari elemen komentar di halaman show atau modal
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;

        if (!confirm('Yakin ingin menghapus komentar ini?')) return;

        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentElement.remove();
                // Update jumlah komentar di halaman show atau modal
                const countSpan = document.getElementById('commentCount') || document.querySelector('.modal-content #commentCount');
                if (countSpan) {
                    countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
                }
            } else {
                throw new Error(data.message || 'Gagal menghapus komentar.');
            }
        })
        .catch(error => {
            console.error('Delete Comment Error:', error);
            alert('Gagal menghapus komentar: ' + (error.message || 'Coba lagi nanti.'));
        });
    }
});

// --- Fungsi Copy Link ---
function copyLink() {
    const url = window.location.href.replace('/modal', '');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert('✅ Link berhasil disalin!');
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
    alert('✅ Link disalin!');
}

// Pastikan fungsi ini tersedia global jika dipanggil dari onclick inline
window.handleCommentSubmit = handleCommentSubmit; // Tidak digunakan lagi, tapi bisa tetap ada untuk kompatibilitas
window.handleLikeToggle = handleLikeToggle; // Tidak digunakan lagi, tapi bisa tetap ada untuk kompatibilitas
window.copyLink = copyLink;