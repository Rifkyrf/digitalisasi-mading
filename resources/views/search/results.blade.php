@extends('layouts.app')

@section('content')
<body class="search-mode"> <!-- 👈 tambahkan class ini -->
<div class="container py-4">
    <div class="d-flex mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h5 class="ms-2 mt-1">Hasil Pencarian</h5>
    </div>

    <!-- Form Pencarian -->
    <form action="{{ route('search.results') }}" method="GET" class="mb-4">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" class="form-control"
                   placeholder="Cari nama, email, atau NIS..."
                   value="{{ request('q') }}" required>
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <!-- Hasil -->
    @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
            <p class="text-muted">Tidak ada pengguna ditemukan untuk "<strong>{{ $query }}</strong>"</p>
        </div>
    @else
        <div class="row">
            @foreach($users as $user)
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
            @endforeach
        </div>
    @endif
</div>
@endsection