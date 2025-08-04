<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
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
</head>

<body class="bg-neutral-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <!-- Logo -->
            <a href="#" class="text-2xl font-bold text-indigo-600">Engage<span class="text-neutral-800">English</span></a>

            <!-- Profile Section -->
            <div class="flex items-center gap-4">
                <p class="text-neutral-700">{{ Auth::user()->name ?? 'Guest' }}</p>

                <!-- Wadah Relative untuk Dropdown -->
                <div class="relative">
                    <button id="profile-button" class="flex items-center focus:outline-none">
                        <img class="w-10 h-10 rounded-full object-cover" src="https://i.pravatar.cc/150?img=5" alt="Foto Profil Pengguna">
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Profil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Dashboard</a>
                        <div class="border-t border-neutral-200 my-1"></div>

                        <!-- FORM LOGOUT DIMULAI DI SINI -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </a>
                        </form>
                        <!-- FORM LOGOUT SELESAI -->
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-neutral-800">Selamat Datang Kembali!</h1>
        <p class="text-neutral-600 mt-2">Lanjutkan proses belajarmu dan capai targetmu.</p>

        <!-- Placeholder untuk konten dasbor -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($modules as $module)
            @if($module->is_locked)
            <div class="p-6 bg-neutral-100 border border-neutral-200 rounded-lg shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold text-neutral-400">{{ $module->title }}</h2>
                    <p class="mt-2 text-sm text-neutral-400">{{ $module->description }}</p>
                </div>
                <div class="mt-4 flex items-center justify-center gap-2 bg-neutral-200 text-neutral-500 text-center py-2 px-4 rounded-lg text-sm font-semibold">
                    <i class="fas fa-lock"></i>
                    <span>Terkunci</span>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-1 transition-transform duration-300">
                <div class="p-6 flex-grow">
                    <span class="text-sm font-semibold text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">{{ ucfirst($module->level) }}</span>
                    <h2 class="text-xl font-bold text-neutral-800 mt-4 mb-2">{{ $module->title }}</h2>
                    <p class="text-neutral-600 text-sm flex-grow">{{ $module->description }}</p>
                </div>
                <div class="px-6 pb-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-neutral-700">Progress</span>
                        <span class="text-sm font-medium text-neutral-700">{{ round($module->progress) }}%</span>
                    </div>
                    <div class="w-full bg-neutral-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $module->progress }}%"></div>
                    </div>
                </div>
                <div class="bg-neutral-50 p-4 border-t border-neutral-200 flex justify-between items-center">
                    <span class="text-sm text-neutral-500">
                        <i class="fas fa-book-open mr-2"></i>{{ $module->lessons_count }} Pelajaran
                    </span>
                    <a href="{{ route('modules.show', $module) }}" class="bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition duration-300">Mulai Belajar</a>
                </div>
            </div>
            @endif
            @empty
            <!-- Tampilkan pesan ini jika tidak ada modul yang ditemukan -->
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                <p class="text-neutral-500 text-lg">Oops! Sepertinya belum ada modul yang tersedia saat ini.</p>
            </div>
            @endforelse
        </div>
    </main>

    <script>
        // Ambil elemen yang dibutuhkan
        const profileButton = document.getElementById('profile-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        // Tambahkan event listener untuk tombol profil
        profileButton.addEventListener('click', () => {
            // Toggle class 'hidden' untuk menampilkan/menyembunyikan dropdown
            dropdownMenu.classList.toggle('hidden');
        });

        // Sembunyikan dropdown jika pengguna mengklik di luar area dropdown
        window.addEventListener('click', (event) => {
            if (!profileButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>

</body>

</html>