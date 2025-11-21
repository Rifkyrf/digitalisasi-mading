@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
<div class="container py-4">
    <h2><i class="fas fa-user-plus"></i> Tambah User Baru</h2>
    <form action="{{ route('admin.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3" id="nisField" style="display: {{ old('role') === 'guest' ? 'none' : 'block' }};">
            <label>NIS/NIP</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" {{ old('role') === 'guest' ? '' : 'required' }}>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" id="roleSelect" required>
                <option value="">-- Pilih --</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="guest" {{ old('role') === 'guest' ? 'selected' : '' }}>Guest</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const nisField = document.getElementById('nisField');
    if (this.value === 'guest') {
        nisField.style.display = 'none';
        nisField.querySelector('input').removeAttribute('required');
    } else {
        nisField.style.display = 'block';
        nisField.querySelector('input').setAttribute('required', '');
    }
});
</script>

@if($errors->any())
    <script>
        Swal.fire({
            title: 'Error!',
            html: `
                <ul class="text-start" style="color: #dc3545; list-style: none; padding: 0; font-size: 0.9em;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    </script>
@endif
@endsection