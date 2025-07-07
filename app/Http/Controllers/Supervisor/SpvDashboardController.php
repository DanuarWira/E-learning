<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SpvDashboardController extends Controller
{
    public function index(): View
    {
        $supervisor = Auth::user();

        // Ambil semua pengguna dengan role 'user' dari instansi yang sama
        $users = User::where('role', 'user')
            ->where('instansi', $supervisor->instansi)
            ->get();

        // Hitung progress keseluruhan untuk setiap pengguna
        $users->each(function ($user) {
            $user->overall_progress = $user->getOverallProgress();
        });

        return view('supervisor.dashboard', compact('users'));
    }
}
