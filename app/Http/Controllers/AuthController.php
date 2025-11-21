<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }


public function login(Request $request)
{
    $request->validate([
        'identifier' => 'required|string',
        'password' => 'required|string',
        'login_as' => 'required|in:internal_nis,internal_email,guest',
    ]);

    $identifier = $request->identifier;
    $password = $request->password;
    $loginAs = $request->login_as;

    $user = null;

    if ($loginAs === 'internal_nis') {
        $user = User::where('nis', $identifier)
                    ->whereIn('role', ['siswa', 'guru', 'admin'])
                    ->first();
    } elseif ($loginAs === 'internal_email') {
        $user = User::where('email', $identifier)
                    ->whereIn('role', ['siswa', 'guru', 'admin'])
                    ->first();
    } else {
        // guest
        $user = User::where('email', $identifier)
                    ->where('role', 'guest')
                    ->first();
    }

    if ($user && Hash::check($password, $user->password)) {
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role === 'guest') {
            // Ganti '/guest-home' dengan route yang sesuai untuk guest
            // Misalnya, halaman profil mereka sendiri atau halaman khusus guest
            return redirect('/profile/' . $user->id)->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
        }

        // Redirect default untuk role lain
        return redirect('/dashboard')->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
    }

    $errorMessage = match($loginAs) {
        'internal_nis' => 'NIS atau password salah.',
        'internal_email' => 'Email atau password salah (untuk akun guru/siswa/admin).',
        default => 'Email atau password salah.',
    };

    return back()->withErrors([
        'identifier' => $errorMessage,
    ])->withInput($request->only('identifier', 'login_as'));
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}