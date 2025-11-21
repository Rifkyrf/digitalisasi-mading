@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user-edit me-2"></i> Edit Profil</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update', Auth::id()) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Foto Profil -->
                        <div class="text-center mb-4">
                            <img src="{{ $user->profile_photo_url }}"
                                 alt="Foto Profil"
                                 class="rounded-circle mx-auto"
                                 style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #0d47a1;">
                            <input type="file" name="profile_photo" class="form-control mt-2" accept="image/*">
                            @error('profile_photo')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio (Opsional)</label>
                            <textarea name="bio" id="bio" class="form-control" rows="3"
                                      placeholder="Jelaskan dirimu...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show', $user->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection