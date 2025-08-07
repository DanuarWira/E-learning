<aside class="w-64 flex-shrink-0 bg-white text-neutral-500 p-4 flex flex-col">
    <div class="text-center py-4 mb-4">
        <a href="{{ route('superadmin.dashboard') }}" class="text-2xl font-bold text-neutral-900">Engage<span class="text-indigo-400">Admin</span></a>
    </div>
    <nav class="flex-grow">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-neutral-50' }} transition-colors">
                    <i class="fas fa-tachometer-alt fa-fw"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="pt-4">
                <span class="px-4 text-xs font-semibold uppercase text-neutral-500">Manajemen Konten</span>
            </li>
            <li>
                <a href="{{ route('superadmin.modules.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.modules.*') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-neutral-50' }} transition-colors">
                    <i class="fas fa-layer-group fa-fw"></i>
                    <span>Kelola Modul</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.lessons.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.lessons.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book-open fa-fw"></i>
                    <span>Kelola Lesson</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.vocabularies.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.vocabularies.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Kelola Vocabulary</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.materials.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.materials.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Kelola Material</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.exercises.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.exercises.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Kelola Exercise</span>
                </a>
            </li>
            <li class="pt-4">
                <span class="px-4 text-xs font-semibold uppercase text-neutral-500">Manajemen Institusi</span>
            </li>
            <li>
                <a href="{{ route('superadmin.instansis.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.instansis.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Kelola Institusi</span>
                </a>
            </li>
            <li class="pt-4">
                <span class="px-4 text-xs font-semibold uppercase text-neutral-500">Manajemen Pengguna</span>
            </li>
            <li>
                <a href="{{ route('superadmin.users.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('superadmin.users.*') ? 'bg-indigo-600 text-white font-semibold shadow-md' : 'hover:bg-neutral-100' }} transition-colors">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Kelola pengguna</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="pt-4 border-t border-neutral-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-neutral-50 transition-colors"
                onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="fas fa-sign-out-alt fa-fw"></i>
                <span>Logout</span>
            </a>
        </form>
    </div>
</aside>