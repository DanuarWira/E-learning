<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Lesson</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-neutral-100 font-sans">
    <div x-data="{
        isModalOpen: false, 
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        lesson: { module_id: '', title: '', slug: '' }
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen Lesson</h1>
                        <p class="text-neutral-500">Buat, edit, dan hapus lesson.</p>
                    </div>
                    <button @click="isModalOpen = true; isEditMode = false; modalTitle = 'Buat Lesson Baru'; formAction = '{{ route('superadmin.lessons.store') }}'; lesson = { module_id: '', title: '', slug: '' };" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i> Buat Baru
                    </button>
                </header>

                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500">
                        <thead class="text-xs text-neutral-500 uppercase bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 font-medium">Judul Lesson</th>
                                <th class="px-6 py-3 font-medium">Modul Induk</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($lessons as $l)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $l->title }}</td>
                                <td class="px-6 py-4">{{ $l->module->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="isModalOpen = true; isEditMode = true; modalTitle = 'Edit Lesson'; formAction = '{{ route('superadmin.lessons.update', $l) }}'; lesson = {{ json_encode($l) }};" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.lessons.destroy', $l) }}" method="POST" onsubmit="return confirm('Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center">Belum ada lesson.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">{{ $lessons->links('vendor.pagination.tailwind') }}</div>
                </div>
            </main>
        </div>

        <!-- Modal Form -->
        <div x-show="isModalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/20">
            <div @click.away="isModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-8 m-4">
                <h2 class="text-2xl font-bold text-neutral-800 mb-6" x-text="modalTitle"></h2>
                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEditMode">@method('PUT')</template>

                    <div class="space-y-6">
                        <div>
                            <label for="module_id" class="block text-sm font-medium">Pilih Modul Induk</label>
                            <select name="module_id" id="module_id" x-model="lesson.module_id" required class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Modul --</option>
                                @foreach($modules as $module)<option value="{{ $module->id }}">{{ $module->title }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label for="title" class="block text-sm font-medium">Judul Lesson</label>
                            <input type="text" name="title" id="title" x-model="lesson.title" required class="mt-1 block w-full border-neutral-300 rounded-md">
                        </div>
                        <div>
                            <label for="slug" class="block text-sm font-medium">Slug</label>
                            <input type="text" name="slug" id="slug" x-model="lesson.slug" required class="mt-1 block w-full border-neutral-300 rounded-md">
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