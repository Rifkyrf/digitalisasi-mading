@extends('layouts.login')

@section('title', 'Login Siswa, Guru & Tamu')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
    }

    /* Modifikasi: Gunakan gambar sebagai background */
    .school-bg {
        height: 100vh;
        background-image: url('{{ asset('images/sekolah_hero.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 2rem;
        position: relative;
    }

    .school-bg-content {
        position: relative;
        z-index: 1;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7),
                     -1px -1px 3px rgba(0, 0, 0, 0.7),
                     1px -1px 3px rgba(0, 0, 0, 0.7),
                     -1px 1px 3px rgba(0, 0, 0, 0.7);
    }

    .school-bg h2 {
        font-weight: 600;
        font-size: 1.8rem;
        line-height: 1.4;
        margin: 0;
    }

    .school-bg p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-top: 1rem;
        max-width: 320px;
    }

    /* === LOGIN CARD === */
    .login-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        width: 100%;
        max-width: 420px;
        overflow: hidden;
        transition: transform 0.3s ease;
        padding: 2rem;
    }

    .login-card:hover {
        transform: translateY(-2px);
    }

    .card-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .brand-logo {
        font-size: 1.4rem;
        font-weight: 600;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .brand-logo i {
        color: #3b82f6;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.95rem;
        color: #334155;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .form-control {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        font-weight: 500;
        color: #1e293b;
        background-color: #f8fafc;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
        width: 100%;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    /* === INPUT PASSWORD DENGAN IKON DI DALAM === */
    .input-password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-password-wrapper input {
        flex: 1;
        padding-right: 40px; /* Beri ruang untuk tombol */
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
    }

    .input-password-wrapper button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        color: #64748b;
        font-size: 1rem;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-password-wrapper button:hover {
        color: #3b82f6;
    }

    .btn-login {
        background: #3b82f6;
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: background-color 0.25s ease, transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-login:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.4rem;
        padding: 0.5rem;
        background: #fef2f2;
        border-radius: 8px;
        border-left: 3px solid #f87171;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .login-mode {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .login-mode-btn {
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 20px;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .login-mode-btn.active {
        background: #dbeafe;
        color: #1e40af;
        font-weight: 600;
    }

    .login-mode-btn.guest.active {
        background: #cffafe;
        color: #0e7490;
    }

    .register-link {
        text-align: center;
        margin-top: 1.25rem;
        font-size: 0.95rem;
        color: #64748b;
    }

    .register-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .register-link a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .school-bg {
            display: none;
        }
        .login-card {
            margin: 1rem;
            width: 90%;
            padding: 1.5rem;
        }
    }
</style>

<div class="row g-0 vh-100">
    <!-- Kolom Kiri -->
    <div class="col-md-6 d-none d-md-flex school-bg">
        <div class="school-bg-content">
            <h2>SMK Bakti Nusantara 666</h2>
            <p>Mencetak Generasi Unggul dan Berkarakter</p>
        </div>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="login-card">
            <div class="card-header">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                    Bakti Nusantara 666
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Toggle Mode -->
                <div class="login-mode">
                    <button type="button" class="login-mode-btn active" data-mode="internal_nis">Login NIS</button>
                    <button type="button" class="login-mode-btn" data-mode="internal_email">Login Email</button>
                    <button type="button" class="login-mode-btn guest" data-mode="guest">Login Tamu</button>
                </div>

                <!-- Input Identifier -->
                <div class="form-group">
                    <label for="identifier" class="form-label">
                        <i class="fas fa-id-card"></i> <span id="label-text">NIS</span>
                    </label>
                    <input
                        type="text"
                        name="identifier"
                        id="identifier"
                        class="form-control"
                        value="{{ old('identifier') }}"
                        placeholder="Masukkan NIS Anda"
                        required
                        autofocus
                    >
                    <input type="hidden" name="login_as" id="login_as" value="internal_nis">
                    @error('identifier')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Input Password dengan Ikon Mata DI DALAM -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-password-wrapper">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                        >
                        <button class="toggle-password-btn" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Button Submit -->
                <div class="form-group">
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </div>

                <!-- Lupa Password -->
                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">
                        daftar sekarang?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loginModeButtons = document.querySelectorAll('.login-mode-btn');
    const labelIcon = document.querySelector('.form-label i');
    const labelText = document.getElementById('label-text');
    const identifierInput = document.getElementById('identifier');
    const loginAsInput = document.getElementById('login_as');

    // Default state
    updateForm('internal_nis');

    loginModeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const mode = this.getAttribute('data-mode');
            updateForm(mode);

            // Update active button
            loginModeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    function updateForm(mode) {
        if (mode === 'internal_nis') {
            labelIcon.className = 'fas fa-id-card';
            labelText.textContent = 'NIS';
            identifierInput.placeholder = 'Masukkan NIS Anda';
            loginAsInput.value = 'internal_nis';
        } else if (mode === 'internal_email') {
            labelIcon.className = 'fas fa-envelope';
            labelText.textContent = 'Email';
            identifierInput.placeholder = 'Masukkan email Anda';
            loginAsInput.value = 'internal_email';
        } else if (mode === 'guest') {
            labelIcon.className = 'fas fa-user';
            labelText.textContent = 'Email Tamu';
            identifierInput.placeholder = 'Masukkan email tamu';
            loginAsInput.value = 'guest';
        }
    }

    // =============== SHOW/HIDE PASSWORD ===============
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye', type === 'password');
        this.querySelector('i').classList.toggle('fa-eye-slash', type === 'text');
    });
});
</script>
@endsection