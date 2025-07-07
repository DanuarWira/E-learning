<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Modul</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-neutral-100">
    <!-- x-data mendefinisikan state untuk Alpine.js -->
    <div x-data="{
        isModalOpen: false, 
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        module: { title: '', description: '', level: 'beginner', is_published: false }
    }" class="flex h-screen bg-neutral-100">

        @include('superadmin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-800">Manajemen Modul</h1>
                    <p class="text-neutral-500">Buat, edit, dan hapus modul pembelajaran.</p>
                </div>
                <!-- Tombol ini sekarang memanggil fungsi Alpine.js -->
                <button @click="isModalOpen = true; isEditMode = false; modalTitle = 'Buat Modul Baru'; formAction = '{{ route('superadmin.modules.store') }}'; module = { title: '', description: '', level: 'beginner', is_published: false };" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Modul Baru
                </button>
            </header>

            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <!-- Tabel Daftar Modul -->
            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="w-full text-sm text-left text-neutral-500">
                    <thead class="text-xs text-neutral-500 uppercase bg-neutral-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Judul</th>
                            <th scope="col" class="px-6 py-3 font-medium">Level</th>
                            <th scope="col" class="px-6 py-3 font-medium">Jml. Lesson</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse($modules as $m)
                        <tr class="hover:bg-neutral-50">
                            <th scope="row" class="px-6 py-4 font-semibold text-neutral-900 whitespace-nowrap">{{ $m->title }}</th>
                            <td class="px-6 py-4"><span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">{{ ucfirst($m->level) }}</span></td>
                            <td class="px-6 py-4">{{ $m->lessons_count }}</td>
                            <td class="px-6 py-4">@if($m->is_published) <span class="text-xs font-semibold bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full">Published</span> @else <span class="text-xs font-semibold bg-neutral-200 text-neutral-700 px-2.5 py-0.5 rounded-full">Draft</span> @endif</td>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <!-- Tombol Edit sekarang memanggil fungsi Alpine.js dengan data modul -->
                                <button @click="isModalOpen = true; isEditMode = true; modalTitle = 'Edit Modul'; formAction = '{{ route('superadmin.modules.update', $m) }}'; module = {{ json_encode($m) }};" class="font-medium text-blue-600 hover:underline" title="Edit"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('superadmin.modules.destroy', $m) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-neutral-500">Belum ada modul.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $modules->links('vendor.pagination.tailwind') }}</div>
            </div>
        </main>

        <!-- Modal Form (Create/Edit) -->
        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/20" style="display: none;">
            <div @click.away="isModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-neutral-800" x-text="modalTitle"></h2>
                    <button @click="isModalOpen = false" class="text-neutral-500 hover:text-neutral-800">&times;</button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <!-- Jika mode edit, tambahkan method PUT -->
                    <template x-if="isEditMode">
                        @method('PUT')
                    </template>

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-neutral-700">Judul Modul</label>
                            <input type="text" name="title" id="title" x-model="module.title" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-neutral-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" x-model="module.description" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label for="level" class="block text-sm font-medium text-neutral-700">Level</label>
                            <select name="level" id="level" x-model="module.level" class="mt-1 block w-full px-3 py-2 border border-neutral-300 bg-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_published" id="is_published" value="1" x-model="module.is_published" class="h-4 w-4 text-indigo-600 border-neutral-300 rounded focus:ring-indigo-500">
                            <label for="is_published" class="ml-2 block text-sm text-neutral-900">Publikasikan Modul Ini?</label>
                        </div>
                    </div>
                    <div class="mt-8 pt-5 flex justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="bg-neutral-200 text-neutral-800 py-2 px-6 rounded-lg font-semibold hover:bg-neutral-300">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg shadow font-semibold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>