<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roleNames): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Load relasi hakguna jika belum (opsional tapi aman)
        if ($user->relationLoaded('hakguna')) {
            $hakguna = $user->hakguna;
        } else {
            $hakguna = $user->hakguna()->first();
        }

        // 3. Cek apakah role valid dan sesuai izin
        if (!$hakguna || !in_array($hakguna->name, $roleNames)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}