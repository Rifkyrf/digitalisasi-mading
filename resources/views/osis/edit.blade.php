@extends('layouts.app')

@section('title', 'Edit Anggota OSIS')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Anggota: {{ $member->name }}</h2>
        <a href="{{ route('osis.manage') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card p-4 shadow-sm">
        <form method="POST" action="{{ route('osis.update', $member) }}" enctype="multipart/form-data" id="osisForm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $member->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jabatan</label>
                <select name="role" class="form-control" required>
                    <option value="ketua" {{ (old('role', $member->role) == 'ketua') ? 'selected' : '' }}>Ketua</option>
                    <option value="sekretaris" {{ (old('role', $member->role) == 'sekretaris') ? 'selected' : '' }}>Sekretaris</option>
                    <option value="bendahara" {{ old('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                    <option value="anggota" {{ (old('role', $member->role) == 'anggota') ? 'selected' : '' }}>Anggota</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipe Anggota</label>
                <select name="type" class="form-control" required>
                    <option value="inti" {{ (old('type', $member->type) == 'inti') ? 'selected' : '' }}>7 Inti OSIS</option>
                    <option value="sekbid" {{ (old('type', $member->type) == 'sekbid') ? 'selected' : '' }}>Sekbid</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="text" name="angkatan" class="form-control" value="{{ old('angkatan', $member->angkatan) }}" required>
            </div>

            <div class="mb-3" id="sekbidField" style="{{ (old('type', $member->type) != 'sekbid') ? 'display:none;' : '' }}">
                <label class="form-label">Nama Sekbid</label>
                <input type="text" name="nama_sekbid" class="form-control" value="{{ old('nama_sekbid', $member->nama_sekbid) }}" placeholder="Contoh: Kesiswaan">
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Saat Ini</label><br>
                <img src="{{ $member->photo_url }}" class="rounded" style="height: 150px; width: auto; object-fit: cover;">
            </div>

            <div class="mb-3">
                <label class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <div class="form-text">Biarkan kosong jika tidak ingin mengganti.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Perbarui
                </button>
                <a href="{{ route('osis.manage') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.querySelector('select[name="type"]');
    const sekbidField = document.getElementById('sekbidField');

    function toggleSekbid() {
        if (typeSelect.value === 'sekbid') {
            sekbidField.style.display = 'block';
            sekbidField.querySelector('input').setAttribute('required', 'required');
        } else {
            sekbidField.style.display = 'none';
            sekbidField.querySelector('input').removeAttribute('required');
        }
    }

    toggleSekbid();
    typeSelect.addEventListener('change', toggleSekbid);
});
</script>
@endpush