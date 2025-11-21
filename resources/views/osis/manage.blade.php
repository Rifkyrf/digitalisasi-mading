@extends('layouts.app')

@section('title', 'Kelola OSIS')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Struktur OSIS</h2>
        <a href="{{ route('osis.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Anggota
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filter Angkatan -->
    <div class="mb-4">
        <label class="form-label">Pilih Angkatan</label>
        <select class="form-control" onchange="location = this.value;">
            @foreach($angkatanList as $angkatan)
                <option value="{{ route('osis.manage') }}?angkatan={{ $angkatan }}"
                        {{ $angkatan == $angkatanAktif ? 'selected' : '' }}>
                    {{ $angkatan }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- 7 Inti -->
    <h4 class="mt-5">7 Pengurus Inti</h4>
    @if($inti->isEmpty())
        <p class="text-muted">Belum ada pengurus inti untuk angkatan {{ $angkatanAktif }}.</p>
    @else
        <div class="row g-3">
            @foreach($inti as $member)
                <div class="col-md-4">
                    <div class="card">
                        <img src="{{ $member->photo_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5>{{ $member->name }}</h5>
                            <p class="text-muted">{{ ucfirst($member->role) }} • {{ $member->angkatan }}</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('osis.edit', $member) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('osis.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Sekbid -->
    <h4 class="mt-5">Seksi Bidang (Sekbid)</h4>
    @if($sekbid->isEmpty())
        <p class="text-muted">Belum ada anggota sekbid untuk angkatan {{ $angkatanAktif }}.</p>
    @else
        @foreach($sekbid as $namaSekbid => $members)
            <h5 class="mt-4">{{ $namaSekbid }}</h5>
            <div class="row g-3">
                @foreach($members as $member)
                    <div class="col-md-4">
                        <div class="card">
                            <img src="{{ $member->photo_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5>{{ $member->name }}</h5>
                                <p class="text-muted">{{ ucfirst($member->role) }} • {{ $member->angkatan }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('osis.edit', $member) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('osis.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection