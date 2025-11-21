@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-cog me-2"></i>Admin Panel</h2>
            <p class="text-muted mb-0">Kelola semua data user</p>
        </div>
    </div>

    <!-- Action Buttons & Search -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.create') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Tambah User Baru
                </a>
                <a href="{{ route('admin.import.form') }}" class="btn btn-info">
                    <i class="fas fa-file-excel me-1"></i> Import dari Excel
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Search Form -->
            <form method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau NIS/NIP..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Semua User</h5>
            <span class="badge bg-light text-primary">{{ $users->total() }} User</span>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada user terdaftar.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NIS/NIP</th>
                            <th>Role</th>
                            <th class="text-center">Karya Diunggah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="fw-medium">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->nis ?? '-' }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-secondary">Admin</span>
                                @elseif($user->role === 'guru')
                                    <span class="badge bg-primary">Guru</span>
                                @elseif($user->role === 'siswa')
                                    <span class="badge bg-success">Siswa</span>
                                @elseif($user->role === 'guest')
                                    <span class="badge bg-warning text-dark">Guest</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $user->works_count }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus user ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} user
                </div>
                <div class="d-flex justify-content-center">
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection