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

        // Hitung progress untuk setiap modul
        foreach ($modules as $module) {
            $lessonProgresses = [];
            foreach ($module->lessons as $lesson) {
                $lessonProgresses[] = $lesson->getProgressFor($user);
            }

            if (count($lessonProgresses) > 0) {
                $module->progress = array_sum($lessonProgresses) / count($lessonProgresses);
            } else {
                $module->progress = 0;
            }
        }

        return view('dashboard', compact('modules'));
    }
}
