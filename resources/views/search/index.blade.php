@extends('layouts.app')

@section('title', 'Cari Pengguna')

@section('content')
<div class="container py-4">
    <div class="d-flex mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h5 class="ms-2 mt-1">Cari</h5>
    </div>

    <form action="{{ route('search.results') }}" method="GET" class="mb-4">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Cari nama, email, atau NIS..."
                   value="{{ request('q') }}" required>
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </div>
    </form>

    @if(request('q'))
        <div class="row">
            @forelse($users as $user)
                <div class="col-12 mb-3">
                    <a href="{{ route('profile.show', $user->id) }}" class="text-decoration-none text-dark">
                        <div class="d-flex align-items-center p-3 border rounded">
                            <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0d47a1&color=fff' }}"
                                 class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                            <div>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada pengguna ditemukan untuk "<strong>{{ request('q') }}</strong>"</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">Masukkan kata kunci untuk mencari pengguna</p>
        </div>
    @endif
</div>
@endsection