<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roleNames)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Pastikan relasi hakguna diambil
        if (!$user->hakguna || !in_array($user->hakguna->name, $roleNames)) {
            abort(403, 'maksud lu apaan ?');
        }

        return $next($request);
    }
}