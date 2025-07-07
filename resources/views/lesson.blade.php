<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelajaran: {{ $lesson->title }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    <main class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <a href="{{ route('modules.show', $lesson->module) }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Modul: {{ $lesson->module->title }}</a>
            <h1 class="text-4xl font-bold text-neutral-800 mt-2">{{ $lesson->title }}</h1>
        </div>

        <!-- Bagian Kosakata -->
        <div>
            <h2 class="text-2xl font-bold text-neutral-800 mb-4">Vocabulary and Phrases</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($lesson->vocabularies as $vocabulary_category)
                <a href="{{ route('lessons.practice', ['lesson' => $lesson, 'vocabulary' => $vocabulary_category]) }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-neutral-900">{{ $vocabulary_category->category }}</h3>
                        <span class="text-sm font-medium text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">{{ count($vocabulary_category->items) }} kata</span>
                    </div>
                    <p class="mt-2 text-sm text-neutral-600">Mulai belajar kosakata dalam kategori ini.</p>
                    <div class="text-right mt-4 text-indigo-600 font-semibold">
                        Mulai Latihan &rarr;
                    </div>
                </a>
                @empty
                <p class="text-neutral-500 md:col-span-2 lg:col-span-3">Belum ada kosakata untuk pelajaran ini.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 mt-8">Materials</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($lesson->materials as $material_category)
                <a href="{{ route('lessons.material.show', ['lesson' => $lesson, 'material' => $material_category]) }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-neutral-900">{{ $material_category->type }}</h3>
                        <span class="text-sm font-medium text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">{{ count($material_category->items) }} Item</span>
                    </div>
                    <p class="mt-2 text-sm text-neutral-600">Lihat materi dalam kategori ini.</p>
                    <div class="text-right mt-4 text-indigo-600 font-semibold">
                        Lihat Materi &rarr;
                    </div>
                </a>
                @empty
                <p class="text-neutral-500">Belum ada material untuk pelajaran ini.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-neutral-800 mb-4 mt-8">Exercises</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if($lesson->exercises->isNotEmpty())
                <a href="{{ route('lessons.exercise.practice', $lesson) }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-neutral-900">Latihan Terpadu</h3>
                        <span class="text-sm font-medium text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">{{ count($lesson->exercises) }} Latihan</span>
                    </div>
                    <p class="mt-2 text-sm text-neutral-600">Kerjakan semua latihan untuk pelajaran ini dalam satu sesi.</p>
                    <div class="text-right mt-4 text-indigo-600 font-semibold">
                        Mulai Latihan &rarr;
                    </div>
                </a>
                @else
                <p class="text-neutral-500">Belum ada latihan untuk pelajaran ini.</p>
                @endif
            </div>

        </div>
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