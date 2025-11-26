<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register'); // ← pastikan nama view benar
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => 'Guest ' . strtok($request->email, '@'),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => '4',
        ]);

        auth()->login($user);

        return redirect('/login')->with('success', 'Akun guest berhasil dibuat!');
    }
}