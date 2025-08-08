<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
            'total_instansi' => Instansi::count(),
            'total_modules' => Module::count(),
        ];

        return view('superadmin.dashboard', compact('stats'));
    }
}
