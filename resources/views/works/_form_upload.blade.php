<form id="uploadForm" enctype="multipart/form-data">
    @csrf
    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
        <!-- Header -->
        <div class="text-white px-4 py-3" style="background: var(--topbar-bg); color: var(--text-color)">
            <h5 class="mb-0">
                <i class="fas fa-upload me-2"></i>
                Upload Karya Siswa
            </h5>
            <p class="mb-0 opacity-75" style="font-size: 0.85rem;">Bagikan kreativitasmu: coding, video, gambar, atau dokumen</p>
        </div>

        <!-- Body -->
        <div class="card-body p-4">
            <!-- Judul -->
            <div class="mb-4">
                <label for="title" class="form-label fw-semibold">Judul Karya</label>
                <input type="text" name="title" id="title" class="form-control form-control-lg"
                    placeholder="Contoh: Aplikasi To-Do List dengan Laravel" required>
            </div>

            <!-- Deskripsi -->
            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" class="form-control" rows="3"
                    placeholder="Jelaskan karyamu..."></textarea>
            </div>

            <!-- Tipe Konten -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Tipe Konten</label>
                <select name="type" class="form-select form-select-lg" required>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Upload File Utama -->
            <div class="mb-4">
                <label class="form-label fw-semibold">File Karya</label>
                <div class="upload-area text-center p-4 border rounded-3"
                    style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                    onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 2.5rem;"></i>
                    <h6 class="mt-2">Pilih file atau seret ke sini</h6>
                    <p class="text-muted mb-0">Foto, Video, PDF, ZIP, Kode, dll</p>
                    <small class="text-danger">Maks. 500MB</small>
                    <input type="file" name="file" id="fileInput" class="d-none" required
                           onchange="handleFileSelect(this)">
                </div>
                <div class="mt-2 text-center">
                    <small id="fileName" class="text-muted">Belum ada file dipilih</small>
                </div>

                <!-- Preview File -->
                <div id="filePreview" class="mt-3 d-none">
                    <h6 class="text-muted">Preview:</h6>
                    <div id="previewContent"></div>
                </div>
            </div>

            <!-- Thumbnail -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Thumbnail (Opsional)</label>
                <div class="upload-area text-center p-4 border rounded-3"
                    style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                    onclick="document.getElementById('thumbnailInput').click()">
                    <i class="fas fa-image text-success" style="font-size: 2.5rem;"></i>
                    <h6 class="mt-2">Pilih thumbnail</h6>
                    <p class="text-muted mb-0">Gambar kecil untuk mewakili karyamu</p>
                    <small class="text-danger">Maks. 2MB | JPG, PNG, GIF</small>
                    <input type="file" name="thumbnail" id="thumbnailInput" class="d-none"
                           accept="image/*" onchange="previewThumbnail(this)">
                </div>
                <div class="mt-2 text-center">
                    <small id="thumbnailFileName" class="text-muted">Belum ada thumbnail dipilih</small>
                </div>
                <div id="thumbnailPreview" class="mt-3 d-none">
                    <img src="" alt="Thumbnail Preview" class="img-fluid rounded"
                         style="max-height: 200px; object-fit: cover;">
                </div>
            </div>

            <!-- Info -->
            <div class="alert alert-info d-flex align-items-center p-2 mb-4" role="alert" style="font-size: 0.85rem;">
                <i class="fas fa-info-circle me-2"></i>
                Karya akan ditampilkan secara <strong>publik</strong>.
            </div>

            <!-- Error -->
            <div class="alert alert-danger d-none" id="uploadError"></div>

            <!-- Tombol -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 py-2" id="submitBtn">
                    <i class="fas fa-paper-plane me-1"></i> Unggah
                </button>
            </div>
        </div>
    </div>
</form>

<style>
.upload-area:hover {
    background-color: #eef2ff;
    border-color: #0d47a1 !important;
}
.form-select:focus, .form-control:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
}
.btn-primary {
    background-color: #0d47a1;
    border: none;
}
.btn-primary:hover {
    background-color: #0b3c85;
}
/* Loading state untuk button */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
/* Toast notifications */
.toast-container {
    z-index: 9999;
}
</style>

<script>
// Preview file utama (gambar/video)
function handleFileSelect(input) {
    const file = input.files[0];
    const fileName = file ? file.name : 'Belum ada file dipilih';
    document.getElementById('fileName').textContent = 'File: ' + fileName;

    const preview = document.getElementById('filePreview');
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '';
    preview.classList.add('d-none');

    if (!file) return;

    const fileType = file.type;
    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (fileType.startsWith('image/')) {
        preview.classList.remove('d-none');
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'img-fluid rounded';
        img.style.maxHeight = '300px';
        img.style.objectFit = 'cover';
        previewContent.appendChild(img);
    } else if (fileType.startsWith('video/')) {
        preview.classList.remove('d-none');
        const video = document.createElement('video');
        video.controls = true;
        video.className = 'w-100 rounded';
        video.style.maxHeight = '300px';
        const source = document.createElement('source');
        source.src = URL.createObjectURL(file);
        source.type = fileType;
        video.appendChild(source);
        previewContent.appendChild(video);
    } else {
        // Tampilkan ikon file
        preview.classList.remove('d-none');
        const icon = document.createElement('div');
        icon.className = 'text-center';
        icon.innerHTML = `
            <i class="fas fa-file-alt text-secondary" style="font-size: 4rem;"></i>
            <p class="mt-2 text-muted">${fileExtension.toUpperCase()} File</p>
        `;
        previewContent.appendChild(icon);
    }
}

// Preview thumbnail
function previewThumbnail(input) {
    const file = input.files[0];
    const fileName = file ? file.name : 'Belum ada thumbnail dipilih';
    document.getElementById('thumbnailFileName').textContent = fileName;

    const preview = document.getElementById('thumbnailPreview');
    const img = preview.querySelector('img');
    preview.classList.add('d-none');

    if (!file || !file.type.match('image.*')) return;

    preview.classList.remove('d-none');
    img.src = URL.createObjectURL(file);
}

// Submit form
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const errorDiv = document.getElementById('uploadError');
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;

    errorDiv.classList.add('d-none');

    // Disable button dan show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengunggah...';

    try {
        const response = await fetch("{{ route('upload.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest' // Pastikan header ini ada
            },
            body: formData
        });

        // PERBAIKAN: Handle response yang bukan JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const result = await response.json();

            if (result.success) {
                // Show success message
                showToast('success', result.message || '🎉 Karya berhasil diunggah! tunggu admin atau guru menverifikasi');

                // Close modal dan refresh
                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                if (modal) modal.hide();

                // Refresh halaman setelah delay singkat
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } else {
                throw new Error(result.message || 'Gagal mengunggah karya.');
            }
        } else {
            // Jika response HTML, berarti ada redirect atau error
            const text = await response.text();
            if (response.ok) {
                // Redirect terjadi, reload halaman
                window.location.reload();
            } else {
                throw new Error('Terjadi kesalahan pada server.');
            }
        }

    } catch (err) {
        console.error('Upload error:', err);
        errorDiv.textContent = err.message || 'Terjadi kesalahan jaringan.';
        errorDiv.classList.remove('d-none');

        // Scroll ke error message
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
});

// Fungsi untuk show toast notification
function showToast(type, message) {
    // Cek jika toast container sudah ada
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

    // Remove element setelah hide
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}


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