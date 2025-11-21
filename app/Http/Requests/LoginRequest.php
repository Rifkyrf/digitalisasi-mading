<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'nis' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Cari user berdasarkan nis
        $user = \App\Models\User::where('nis', $this->nis)->first();

        if (!$user || !\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'nis' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Login dengan objek user → Auth::id() akan ambil 'id' asli (misal: 3)
        Auth::login($user, $this->boolean('remember'));
    }

    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

        throw ValidationException::withMessages([
            'nis' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($this->throttleKey())]),
        ]);
    }

    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->input('nis')).'|'.$this->ip());
    }
}