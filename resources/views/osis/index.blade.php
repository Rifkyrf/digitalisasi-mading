@extends('layouts.app')

@section('title', 'Struktur OSIS')

@section('content')
<div class="container mt-5 pt-5">
    <h2 class="text-center mb-4">Struktur Inti OSIS</h2>

    @if($inti->isEmpty())
        <div class="alert alert-info text-center">Belum ada data pengurus inti untuk angkatan ini.</div>
    @else
        @foreach($inti as $member)
            <div class="d-flex align-items-center mb-4 {{ $loop->even ? 'flex-row-reverse flex-md-row' : '' }}">
                <img src="{{ $member->photo_url }}" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #0d6efd;">
                <div>
                    <h5>{{ $member->name }}</h5>
                    <p class="text-muted mb-0">{{ ucfirst($member->role) }} • Angkatan {{ $member->angkatan }}</p>
                </div>
            </div>
        @endforeach
    @endif

    @if($angkatanList->count() > 1)
        <div class="mt-4 text-center">
            <p>Lihat angkatan lain:</p>
            <div class="btn-group" role="group">
                @foreach($angkatanList as $angkatan)
                    <a href="{{ route('osis.index', ['angkatan' => $angkatan]) }}"
                       class="btn btn-sm {{ $angkatan == request('angkatan', date('Y').'/'.(date('Y')+1)) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $angkatan }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection