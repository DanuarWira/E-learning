<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    // Terima parameter role yang diizinkan
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika pengguna tidak login atau tidak memiliki role yang diizinkan,
        // alihkan atau tampilkan halaman error.
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Anda bisa arahkan ke halaman lain, misalnya:
            // return redirect('/dashboard')->with('error', 'Anda tidak punya akses.');
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        return $next($request);
    }
}
