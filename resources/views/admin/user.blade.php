@extends('layouts.app')

@section('title', 'Detail ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <a href="{{ route('admin.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>{{ $user->name }}</h4>
                    <p>
                        <strong>Role:</strong>
                        <span class="badge {{ $user->role === 'guru' ? 'bg-primary' : 'bg-success' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                    <p><strong>NIS/NIP:</strong> {{ $user->nis ?? '–' }}</p>
                    <p><strong>Email:</strong> {{ $user->email ?? '–' }}</p>
                    <p><strong>Total Karya:</strong> <strong>{{ $user->works->count() }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mt-4">Daftar Karya</h5>
    @if($user->works->isEmpty())
        <p class="text-muted">Belum pernah upload karya.</p>
    @else
        <div class="row">
            @foreach($user->works as $work)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="{{ $work->thumbnail }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title">{{ Str::limit($work->title, 30) }}</h6>
                        <small class="text-muted">{{ $work->file_type }} • {{ now()->diffForHumans($work->created_at) }}</small>
                        <a href="{{ route('work.show', $work->id) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                            Lihat
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection