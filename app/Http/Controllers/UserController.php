<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Ambil semua user kecuali superadmin yang sedang login, beserta relasi instansi
        $users = User::where('role', '!=', 'superadmin')
            ->with('instansi')
            ->latest()
            ->paginate(10);

        $instansis = Instansi::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'instansis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'instansi_id' => ['required', 'exists:instansis,id'],
            'role' => ['required', 'in:supervisor,user'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'instansi_id' => $request->instansi_id,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'instansi_id' => ['required', 'exists:instansis,id'],
            'role' => ['required', 'in:supervisor,user'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $userData = $request->except('password', 'password_confirmation');

        // Jika password diisi, hash dan update passwordnya
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Tambahan keamanan agar superadmin tidak bisa menghapus dirinya sendiri
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak dapat menghapus akun superadmin.');
        }

        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
