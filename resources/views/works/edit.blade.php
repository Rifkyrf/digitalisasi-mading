@extends('layouts.app')

@section('title', 'Edit Karya')

@section('content')
<div class="container-fluid py-4" style="background-color: #f0f2f5; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="text-white px-4 py-3" style="background: var(--topbar-bg); color: var(--text-color)">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Karya</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('works.update', $work->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $work->title) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Jelaskan karyamu...">{{ old('description', $work->description) }}</textarea>
                        </div>


                        <select name="type" class="form-control" required>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ $work->type == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>

                        <!-- Ganti File Utama -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ganti File Utama (Opsional)</label>
                            <div class="upload-area text-center p-5 border rounded-3"
                                style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                                onclick="document.getElementById('newFileInput').click()">
                                <i class="fas fa-file-alt text-primary" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Pilih file baru atau seret ke sini</h5>
                                <p class="text-muted mb-0">Maksimal 500MB</p>
                                <input type="file" name="file" id="newFileInput" class="d-none" onchange="updateNewFileName(this);">
                            </div>
                            <div class="mt-2 text-center">
                                <small id="newFileName" class="text-muted">
                                    @if($work->file_path)
                                        File saat ini: {{ basename($work->file_path) }}
                                    @else
                                        Tidak ada file
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Ganti Thumbnail -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ganti Thumbnail (Opsional)</label>
                            <div class="upload-area text-center p-5 border rounded-3"
                                style="border-style: dashed !important; cursor: pointer; background: #f8f9ff;"
                                onclick="document.getElementById('newThumbnailInput').click()">
                                <i class="fas fa-image text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Pilih thumbnail baru atau seret ke sini</h5>
                                <p class="text-muted mb-0">Gunakan gambar kecil untuk mewakili karyamu</p>
                                <input type="file" name="thumbnail" id="newThumbnailInput" class="d-none" accept="image/*" onchange="updateNewThumbnailName(this); previewNewThumbnail(this);">
                            </div>

                            <!-- Preview Thumbnail Baru -->
                            <div id="newThumbnailPreview" class="mt-3 d-none">
                                <h6 class="text-muted">Preview Thumbnail Baru:</h6>
                                <img src="" alt="New Thumbnail Preview" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>

                            <!-- Thumbnail Saat Ini -->
                            <div class="mt-3">
                                <h6 class="text-muted">Thumbnail Saat Ini:</h6>
                                @if($work->thumbnail_path)
                                    <img src="{{ Storage::url($work->thumbnail_path) }}" alt="Current Thumbnail" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                @else
                                    <img src="{{ $work->thumbnail }}" alt="Default Thumbnail" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                @endif
                            </div>

                            <div class="mt-2 text-center">
                                <small id="newThumbnailFileName" class="text-muted">Biarkan kosong jika tidak ingin ganti</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('work.show', $work->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateNewFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : 'Tidak ganti file';
    document.getElementById('newFileName').textContent = 'File baru: ' + fileName;
}

function updateNewThumbnailName(input) {
    const fileName = input.files[0] ? input.files[0].name : 'Biarkan kosong jika tidak ingin ganti';
    document.getElementById('newThumbnailFileName').textContent = fileName;
}

function previewNewThumbnail(input) {
    const file = input.files[0];
    const preview = document.getElementById('newThumbnailPreview');
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
@endsection