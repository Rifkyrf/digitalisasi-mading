<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Debug: cek apakah request masuk
        \Log::info('Login attempt', ['nis' => $request->nis]);

        $request->authenticate();

        $request->session()->regenerate();

        // Debug: login sukses
        \Log::info('Login success', ['user_id' => Auth::id()]);

        return redirect('/dashboard')->with('success', 'Login berhasil! Selamat datang kembali.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}