@extends('layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Verifikasi OTP</h4>
                </div>
                <div class="card-body">
                    <p>Masukkan kode OTP yang telah dikirim ke email Anda.</p>

                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.otp.verify.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ session('email') ?? old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="otp" class="form-label">Kode OTP</label>
                            <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" required>
                            @error('otp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Verifikasi OTP</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="{{ route('password.otp.request') }}">Kirim Ulang OTP</a> |
                        <a href="{{ route('login') }}">Kembali ke Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection