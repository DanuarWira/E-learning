<?php

namespace App\Http\Controllers;

use App\Models\Module; // <-- Jangan lupa impor Model Module
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index(): View
    {
        $user = Auth::user();
        $modules = Module::where('is_published', true)
            ->with(['lessons.vocabularies.items', 'lessons.materials.items', 'lessons.exercises']) // Eager load semua yang dibutuhkan
            ->get();
        $previousModuleIsComplete = true;

        foreach ($modules as $module) {
            $module->is_locked = !$previousModuleIsComplete;

            $module->progress = $module->is_locked ? 0 : $module->getProgressForUser($user);

            if (!$module->is_locked) {
                $previousModuleIsComplete = $module->isCompleteFor($user);
            }
        }

        return view('dashboard', compact('modules'));
    }
}
