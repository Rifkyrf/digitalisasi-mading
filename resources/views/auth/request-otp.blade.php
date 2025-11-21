@extends('layouts.app')

@section('title', 'Request OTP')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reset Password</h4>
                </div>
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                        <div class="text-center">
                            <p>Mengalihkan ke halaman verifikasi...</p>
                        </div>
                        <script>
                            // ✅ Redirect otomatis setelah 2 detik
                            setTimeout(function() {
                                window.location.href = "{{ route('password.otp.verify') }}";
                            }, 2000);
                        </script>
                    @else
                        <p>Masukkan email Anda untuk menerima kode OTP.</p>

                        <form method="POST" action="{{ route('password.otp.send') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Kirim OTP</button>
                            </div>
                        </form>
                    @endif

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Kembali ke Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection