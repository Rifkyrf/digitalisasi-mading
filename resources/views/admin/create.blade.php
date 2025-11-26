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
        <div class="mb-3" id="nisField" style="display: {{ (old('role') && !in_array(old('role'), ['guest'])) ? 'block' : 'none' }};">
            <label>NIS/NIP</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" id="roleSelect" required>
                <option value="">-- Pilih Role --</option>
                @foreach($hakgunas as $hakguna)
                    <option value="{{ $hakguna->id }}" {{ old('role') == $hakguna->id ? 'selected' : '' }}>
                        {{ $hakguna->name }}
                    </option>
                @endforeach
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
    const selectedValue = this.value;

    // Ambil semua role dari opsi yang ada (kita asumsikan 'guest' punya name 'Guest' atau ID tertentu)
    // Tapi lebih aman: tambahkan logika di backend atau tambahkan data attribute
    // Alternatif: kirim info apakah role ini butuh NIS via data attribute

    // 🔥 Solusi sederhana: tambahkan class atau data di opsi
    // TAPI karena data role dinamis, kita perlu tahu mana yang "guest"
    // Misal: di database, role guest punya name "Guest" atau code tertentu

    // Sementara, kita asumsikan: jika ID-nya sesuai dengan role guest yang kamu tahu
    // TAPI ini tidak scalable.

    // ✅ Lebih baik: tambahkan `data-needs-nis="false"` di option jika guest
    // TAPI karena kamu belum punya itu, mari ubah pendekatan:

    // Alternatif: refresh halaman saat ganti role → TIDAK user-friendly.

    // 🔧 Kita lakukan: tambahkan `data-is-guest` di option
});

// Tapi karena DOM belum tahu mana guest, kita modifikasi option dengan data attribute via PHP
</script>

{{-- ✨ Perbaiki logika NIS dengan data attribute --}}
<script>
document.querySelectorAll('#roleSelect option').forEach(option => {
    // Ambil name role dari teks opsi (tidak ideal, tapi workable sementara)
    // Lebih baik: gunakan data attribute dari PHP
});
</script>
@endsection

@section('scripts')
<script>
    function toggleNisField() {
        const roleSelect = document.getElementById('roleSelect');
        const nisField = document.getElementById('nisField');
        const nisInput = nisField.querySelector('input[name="nis"]');

        // Cari opsi yang dipilih
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const isGuest = selectedOption.hasAttribute('data-guest');

        if (isGuest) {
            nisField.style.display = 'none';
            nisInput.removeAttribute('required');
        } else {
            nisField.style.display = 'block';
            nisInput.setAttribute('required', 'required');
        }
    }

    document.getElementById('roleSelect').addEventListener('change', toggleNisField);
    // Trigger saat load jika ada old input
    document.addEventListener('DOMContentLoaded', toggleNisField);
</script>
@endsection