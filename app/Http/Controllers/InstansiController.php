<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstansiController extends Controller
{
    public function index(): View
    {
        $instansis = Instansi::latest()->paginate(10);
        return view('superadmin.instansis.index', compact('instansis'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:instansis']);
        Instansi::create($request->all());
        return redirect()->route('superadmin.instansis.index')->with('success', 'Instansi berhasil dibuat.');
    }

    public function update(Request $request, Instansi $instansi)
    {
        $request->validate(['name' => 'required|string|max:255|unique:instansis,name,' . $instansi->id]);
        $instansi->update($request->all());
        return redirect()->route('superadmin.instansis.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Instansi $instansi)
    {
        // Periksa apakah ada user yang terkait sebelum menghapus
        if ($instansi->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus instansi karena masih memiliki user terkait.');
        }
        $instansi->delete();
        return redirect()->route('superadmin.instansis.index')->with('success', 'Instansi berhasil dihapus.');
    }
}
