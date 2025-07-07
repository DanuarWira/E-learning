<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Menghitung semua statistik yang dibutuhkan
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
            'total_instansi' => User::distinct()->count('instansi'),
            'total_modules' => Module::count(),
        ];

        // Mengirim data statistik ke view
        return view('superadmin.dashboard', compact('stats'));
    }
}
