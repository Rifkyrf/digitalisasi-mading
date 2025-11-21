<form id="editForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="work_id" id="editWorkId" value="{{ $work->id }}">

    <!-- Judul -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Judul Karya</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $work->title) }}" required maxlength="255">
    </div>

    <!-- Deskripsi -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3" maxlength="1000">{{ old('description', $work->description) }}</textarea>
    </div>

    <!-- Tipe Konten -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Tipe Konten</label>
        <select name="type" class="form-select" required>
            @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ $work->type === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <!-- File Saat Ini -->
    <div class="mb-3">
        <label class="form-label fw-semibold">File Saat Ini</label>
        <div class="alert alert-info">
            <strong>{{ basename($work->file_path) }}</strong>
            <br>
            <small>Tipe: {{ $work->file_type }} | Ukuran: {{ round(Storage::disk('public')->size($work->file_path) / 1024) }} KB</small>
        </div>
    </div>

    <!-- Ganti File -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Ganti File? (Opsional)</label>
        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.webm">
        <div class="form-text">Biarkan kosong jika tidak ingin mengganti file. Maks. 500MB</div>
    </div>

    <!-- Thumbnail Saat Ini -->
    @if($work->thumbnail_path)
    <div class="mb-3">
        <label class="form-label fw-semibold">Thumbnail Saat Ini</label>
        <div class="mt-2">
            <img src="{{ asset('storage/' . $work->thumbnail_path) }}" alt="Thumbnail" class="img-fluid rounded" style="max-height: 150px;">
        </div>
    </div>
    @endif

    <!-- Ganti Thumbnail -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Ganti Thumbnail? (Opsional)</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/*">
        <div class="form-text">Maks. 2MB | JPG, PNG, GIF</div>
    </div>

    <!-- Error Message -->
    <div class="alert alert-danger d-none" id="editError"></div>

    <!-- Tombol -->
    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning" id="editSubmitBtn">
            <i class="fas fa-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>

<script>
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const errorDiv = document.getElementById('editError');
    const submitBtn = document.getElementById('editSubmitBtn');
    const originalBtnText = submitBtn.innerHTML;

    errorDiv.classList.add('d-none');

    // Disable button dan show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    try {
        const workId = document.getElementById('editWorkId').value;

        console.log('🔄 Mengupdate work ID:', workId);

        const response = await fetch(`/works/${workId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });

        console.log('📡 Response Status:', response.status);

        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            const result = await response.json();
            console.log('📡 Response JSON:', result);

            if (result.success) {
                showToast('success', result.message || '🎉 Perubahan berhasil disimpan!');

                // Tutup modal edit
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                if (editModal) editModal.hide();

                // Refresh halaman setelah delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } else {
                throw new Error(result.message || 'Gagal menyimpan perubahan.');
            }
        } else {
            const text = await response.text();
            console.log('📡 Response HTML:', text.substring(0, 200));

            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Terjadi kesalahan pada server.');
            }
        }

    } catch (err) {
        console.error('❌ Edit error:', err);
        errorDiv.textContent = err.message || 'Terjadi kesalahan jaringan.';
        errorDiv.classList.remove('d-none');
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
});

function showToast(type, message) {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}
</script>

<style>
.btn-warning {
    background-color: #ffc107;
    border: none;
    color: #000;
}
.btn-warning:hover {
    background-color: #e0a800;
}
</style>