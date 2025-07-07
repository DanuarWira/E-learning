<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Menangani permintaan login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validasi data input dari form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba untuk mengotentikasi pengguna
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Jika berhasil, regenerate session untuk keamanan
            $request->session()->regenerate();

            // === PERUBAHAN UTAMA ADA DI SINI ===
            // 3. Periksa role pengguna dan arahkan ke dasbor yang sesuai
            $user = Auth::user();
            if ($user->role === 'superadmin') {
                return redirect()->intended(route('superadmin.dashboard'));
            } else if ($user->role === 'supervisor') {
                return redirect()->intended(route('supervisor.dashboard'));
            }

            // Jika role lain, arahkan ke dasbor user biasa
            return redirect()->intended(route('dashboard'));
        }

        // 4. Jika otentikasi gagal
        // Kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menangani permintaan logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login'); // Redirect ke halaman utama setelah logout
    }
}
