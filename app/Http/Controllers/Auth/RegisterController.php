<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman formulir registrasi.
     */
    public function showRegistrationForm()
    {
        return view('register'); // Pastikan Anda punya file resources/views/register.blade.php
    }

    /**
     * Menangani permintaan registrasi.
     */
    public function register(Request $request)
    {
        // 1. Validasi data input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        // 2. Buat user baru
        $user = User::create([
            'name' => $request->name,
            'instansi' => $request->instansi,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Login user yang baru dibuat
        Auth::login($user);

        // 4. Redirect ke halaman dashboard
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login untuk melanjutkan.');
    }
}
