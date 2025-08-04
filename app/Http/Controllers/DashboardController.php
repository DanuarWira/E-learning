<?php

namespace App\Http\Controllers;

use App\Models\Module; // <-- Jangan lupa impor Model Module
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dasbor dengan daftar modul.
     */
    public function index(): View
    {
        $user = Auth::user();
        $modules = Module::where('is_published', true)
            ->with(['lessons.vocabularies.items', 'lessons.materials.items', 'lessons.exercises']) // Eager load semua yang dibutuhkan
            ->get();
        $previousModuleIsComplete = true;
        // Hitung progress untuk setiap modul
        foreach ($modules as $module) {
            // Tentukan status terkunci berdasarkan modul sebelumnya
            $module->is_locked = !$previousModuleIsComplete;

            // Hitung progress hanya jika tidak terkunci
            $module->progress = $module->is_locked ? 0 : $module->getProgressForUser($user);

            // Siapkan status untuk iterasi berikutnya
            if (!$module->is_locked) {
                $previousModuleIsComplete = $module->isCompleteFor($user);
            }
        }

        return view('dashboard', compact('modules'));
    }
}
