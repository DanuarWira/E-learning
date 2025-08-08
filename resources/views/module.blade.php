<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Sedikit transisi untuk dropdown */
        .transition-enter-active,
        .transition-leave-active {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .transition-enter-from,
        .transition-leave-to {
            opacity: 0;
            transform: translateY(-10px);
        }

        .transition-enter-to,
        .transition-leave-from {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @vite('resources/css/app.css')
</head>

<body class="bg-neutral-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <!-- Logo -->
            <a href="#" class="text-2xl font-bold text-indigo-600">Engage<span class="text-neutral-800">English</span></a>

            <!-- Profile Section -->
            <div class="flex items-center gap-4">
                <button id="lang-switcher" class="px-3 py-1 border-2 border-indigo-600 text-indigo-600 font-semibold rounded-full text-sm shrink-0">EN</button>
                <p class="text-neutral-700">{{ Auth::user()->name ?? 'Guest' }}</p>

                <!-- Wadah Relative untuk Dropdown -->
                <div class="relative">
                    <button id="profile-button" class="flex items-center focus:outline-none">
                        <img class="w-10 h-10 rounded-full object-cover" src="https://i.pravatar.cc/150?img=5" alt="Foto Profil Pengguna">
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100" data-translate="profile">Profil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100" data-translate="dashboard">Dashboard</a>
                        <div class="border-t border-neutral-200 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100"
                                data-translate="logout"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8">
        <!-- Header Modul -->
        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="text-sm text-indigo-600 hover:underline" data-translate="backToDashboard">&larr; Kembali ke Dashboard</a>
            <h1 class="text-4xl font-bold text-neutral-800 mt-2">{{ $module->title }}</h1>
            <p class="text-neutral-600 mt-2">{{ $module->description }}</p>
        </div>

        <!-- Daftar Pelajaran (Lessons) -->
        <div class="bg-white rounded-lg shadow-lg">
            <ul class="divide-y divide-neutral-200">
                @forelse ($module->lessons as $lesson)
                <li>
                    @if($lesson->is_locked)
                    <!-- Tampilan Kartu Lesson Terkunci -->
                    <div class="block p-6 bg-white rounded-lg shadow-md opacity-50 cursor-not-allowed">
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-semibold text-neutral-400">{{ $lesson->title }}</p>
                            <i class="fas fa-lock text-neutral-400"></i>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('lessons.show', $lesson) }}" class="block hover:bg-neutral-50 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full text-indigo-600 font-bold">
                                    {{ $lesson->order }}
                                </span>
                                <div>
                                    <p class="text-lg font-semibold text-neutral-900">{{ $lesson->title }}</p>
                                    <p class="text-sm text-neutral-500" data-translate="startLesson">Mulai pelajaran ini untuk melanjutkan.</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-neutral-400"></i>
                        </div>
                    </a>
                    @endif
                </li>
                @empty
                <li class="p-6 text-center text-neutral-500" data-translate="noLessons">
                    Belum ada pelajaran yang tersedia untuk modul ini.
                </li>
                @endforelse
            </ul>
        </div>
    </main>

    <script>
        const profileButton = document.getElementById('profile-button');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const langSwitcher = document.getElementById('lang-switcher');
        let currentLanguage = localStorage.getItem('userLanguage') || 'id';

        const translations = {
            en: {
                profile: 'Profile',
                dashboard: 'Dashboard',
                logout: 'Logout',
                backToDashboard: '&larr; Back to Dashboard',
                startLesson: 'Start this lesson to continue.',
                noLessons: 'No lessons available for this module yet.'
            },
            id: {
                profile: 'Profil',
                dashboard: 'Dashboard',
                logout: 'Logout',
                backToDashboard: '&larr; Kembali ke Dashboard',
                startLesson: 'Mulai pelajaran ini untuk melanjutkan.',
                noLessons: 'Belum ada pelajaran yang tersedia untuk modul ini.'
            }
        };

        function updateLanguage() {
            const lang = translations[currentLanguage];
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.dataset.translate;
                if (lang[key]) {
                    el.innerHTML = lang[key];
                }
            });
            langSwitcher.textContent = currentLanguage === 'id' ? 'EN' : 'ID';
        }

        langSwitcher.addEventListener('click', () => {
            currentLanguage = currentLanguage === 'id' ? 'en' : 'id';
            localStorage.setItem('userLanguage', currentLanguage);
            updateLanguage();
        });

        profileButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        window.addEventListener('click', (event) => {
            if (!profileButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', updateLanguage);
    </script>
</body>

</html>