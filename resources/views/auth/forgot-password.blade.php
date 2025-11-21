@extends('layouts.login')

@section('title', 'Lupa Password')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
        }

        .brand-panel {
            flex: 1;
            background: #0ea5e9;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 420px;
            padding: 2.25rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0ea5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
        }

        .label {
            font-weight: 500;
            font-size: 0.95rem;
            color: #334155;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 1rem;
            background: #f8fafc;
            transition: all 0.25s;
        }

        .input:focus {
            outline: none;
            border-color: #0ea5e9;
            background: white;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: #0ea5e9;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #0284c7;
        }

        .error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.4rem;
            padding: 0.5rem;
            background: #fef2f2;
            border-radius: 8px;
        }

        .link-area {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.95rem;
            color: #64748b;
        }

        .link-area a {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 600;
        }

        .link-area a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .brand-panel {
                display: none;
            }
        }
    </style>

    <div class="auth-wrapper">
        <div class="form-panel">
            <div class="card">
                <div class="logo">
                    <i class="fas fa-lock"></i> Lupa Password?
                </div>
                <p class="text-center mb-4">Masukkan email akunmu, kami akan kirim link reset.</p>

                @if (session('status'))
                    <div class="alert alert-success text-center" style="color: #0ea5e9; font-weight: 500;">
                        {{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="label"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" class="input" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn">
                        Kirim Link Reset
                    </button>
                </form>
                <div class="link-area mt-3">
                    <a href="{{ route('login') }}">← Kembali ke Login</a>
                </div>

                <div class="link-area mt-3">
                    <a href="{{ route('password.otp.request') }}">Reset via OTP</a> |
                </div>
            </div>
        </div>
        <div class="brand-panel">
            <h2>Reset Password</h2>
            <p>Kami akan bantu kamu kembali masuk</p>
        </div>
    </div>
@endsection