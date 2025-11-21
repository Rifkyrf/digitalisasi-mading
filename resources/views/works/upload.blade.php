@extends('layouts.app')

@section('title', 'Upload Karya')

@section('content')
<div class="container-fluid py-4" style="background-color: #f0f2f5; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Card Utama -->
            <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                <!-- Header -->
                <div class="text-white px-4 py-3"style="background: var(--topbar-bg); color: var(--text-color)" >
                    <h4 class="mb-0">
                        <i class="fas fa-upload me-2"></i>
                        Upload Karya Siswa
                    </h4>
                    <p class="mb-0 opacity-75" style="font-size: 0.9rem;">Bagikan kreativitasmu: coding, video, gambar, atau dokumen</p>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- Judul -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Judul Karya</label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg"
                                placeholder="Contoh: Aplikasi To-Do List dengan Laravel" required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Deskripsi (Opsional)</label>
                            <textarea name="description" id="description" class="form-control" rows="4"
                                placeholder="Jelaskan karyamu... Apa tujuannya? Bagaimana cara kerjanya?"></textarea>
                        </div>

                        <!-- Tipe Konten -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tipe Konten</label>
                            <select name="type" class="form-control form-control-lg" required>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Upload File -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">File Karya</label>
                            <div class="upload-area text-center p-5 border rounded-3"
                                style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                                onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Pilih file atau seret ke sini</h5>
                                <p class="text-muted mb-0">Dukungan: Foto, Video, ZIP, PDF, DOC, Excel, Kode (Python, JS, dll)</p>
                                <small class="text-danger">Maksimal 500MB</small>
                                <input type="file" name="file" id="fileInput" class="d-none" required
                                       onchange="updateFileName(this)">
                            </div>

                            <!-- Nama File -->
                            <div class="mt-2 text-center">
                                <small id="fileName" class="text-muted">Belum ada file dipilih</small>
                            </div>
                        </div>

                        <!-- Thumbnail Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Thumbnail (Opsional)</label>
                            <div class="upload-area text-center p-5 border rounded-3"
                                style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                                onclick="document.getElementById('thumbnailInput').click()">
                                <i class="fas fa-image text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Pilih thumbnail atau seret ke sini</h5>
                                <p class="text-muted mb-0">Gunakan gambar kecil untuk mewakili karyamu</p>
                                <small class="text-danger">Maksimal 2MB, format: JPG, PNG, GIF</small>
                                <input type="file" name="thumbnail" id="thumbnailInput" class="d-none"
                                       accept="image/*" onchange="updateThumbnailName(this); previewThumbnail(this);">
                            </div>

                            <!-- Preview Thumbnail -->
                            <div id="thumbnailPreview" class="mt-3 d-none">
                                <h6 class="text-muted">Preview Thumbnail:</h6>
                                <img src="" alt="Thumbnail Preview" class="img-fluid rounded"
                                     style="max-height: 200px; object-fit: cover;">
                            </div>

                            <!-- Nama File Thumbnail -->
                            <div class="mt-2 text-center">
                                <small id="thumbnailFileName" class="text-muted">Belum ada thumbnail dipilih</small>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="alert alert-info d-flex align-items-center p-3 mb-4" role="alert"
                             style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                Karya akan ditampilkan secara <strong>publik</strong> dan bisa dilihat semua siswa.
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="d-flex justify-content-between">
                            <a href="/dashboard" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2" style="font-size: 1.1rem;">
                                <i class="fas fa-paper-plane me-2"></i> Unggah Karya
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 text-muted" style="font-size: 0.9rem;">
                © 2025 Bakti Nusantara 666 • Portal Karya Siswa
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
// Update nama file utama
function updateFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : 'Belum ada file dipilih';
    document.getElementById('fileName').textContent = 'File: ' + fileName;
}

// Update nama thumbnail
function updateThumbnailName(input) {
    const fileName = input.files[0] ? input.files[0].name : 'Belum ada thumbnail dipilih';
    document.getElementById('thumbnailFileName').textContent = fileName;
}

// Preview thumbnail
function previewThumbnail(input) {
    const file = input.files[0];
    const preview = document.getElementById('thumbnailPreview');
    const img = preview.querySelector('img');

    if (!file || !file.type.match('image.*')) {
        preview.classList.add('d-none');
        return;
    }

    preview.classList.remove('d-none');
    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

<!-- Style Tambahan -->
<style>
.form-control:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
}

.btn-primary {
    background-color: #0d47a1;
    border: none;
    padding: 10px 30px;
    font-weight: 600;
}

.btn-primary:hover {
    background-color: #0b3c85;
}
</style>
@endsection