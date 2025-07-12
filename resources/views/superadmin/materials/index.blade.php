<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Materials</title>
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
        material: { lesson_id: '', type: 'Teks', items: [{ title: '', description: '', url: '' }] }
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen Materials</h1>
                        <p class="text-neutral-500">Kelola tipe dan item materi.</p>
                    </div>
                    <button @click="isModalOpen = true; isEditMode = false; modalTitle = 'Buat Material Baru'; formAction = '{{ route('superadmin.materials.store') }}'; material = { lesson_id: '', type: 'Teks', items: [{ title: '', description: '', url: '' }] };" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
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
                                <th class="px-6 py-3 font-medium">Tipe Materi</th>
                                <th class="px-6 py-3 font-medium">Lesson Induk</th>
                                <th class="px-6 py-3 font-medium">Jml. Item</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($materials as $mat)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-neutral-900">{{ $mat->type }}</td>
                                <td class="px-6 py-4">{{ $mat->lesson->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $mat->items->count() }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="isModalOpen = true; isEditMode = true; modalTitle = 'Edit Material'; formAction = '{{ route('superadmin.materials.update', $mat) }}'; material = {{ json_encode($mat) }};" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.materials.destroy', $mat) }}" method="POST" onsubmit="return confirm('Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">Belum ada material.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">{{ $materials->links('vendor.pagination.tailwind') }}</div>
                </div>
            </main>
        </div>

        <!-- Modal Form -->
        <div x-show="isModalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/20">
            <div @click.away="isModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-8 m-4 max-h-[90vh] flex flex-col">
                <h2 class="text-2xl font-bold text-neutral-800 mb-6" x-text="modalTitle"></h2>

                <form :action="formAction" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto pr-2">
                    @csrf
                    <template x-if="isEditMode">@method('PUT')</template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="lesson_id" class="block text-sm font-medium">Pilih Lesson Induk</label>
                            <select name="lesson_id" id="lesson_id" x-model="material.lesson_id" required class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Lesson --</option>
                                @foreach($lessons as $lesson)<option value="{{ $lesson->id }}">{{ $lesson->title }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium">Tipe Material</label>
                            <select name="type" id="type-selector" x-model="material.type" required :disabled="isEditMode" class="mt-1 block w-full border-neutral-300 rounded-md shadow-sm disabled:bg-neutral-100">
                                <option value="Teks">Hanya Teks</option>
                                <option value="Audio">Audio</option>
                                <option value="Gambar">Gambar</option>
                                <option value="Video">Video (YouTube)</option>
                            </select>
                        </div>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <h3 class="text-lg font-medium text-neutral-800 pb-2">Item Materi</h3>
                        <template x-for="(item, index) in material.items" :key="index">
                            <div class="item-group grid grid-cols-1 md:grid-cols-2 gap-4 items-start pt-4">
                                <template x-if="isEditMode"><input type="hidden" :name="`items[${index}][id]`" x-model="item.id"></template>
                                <div>
                                    <label :for="`item_title_${index}`" class="block text-sm font-medium">Judul (opsional)</label>
                                    <input type="text" :name="`items[${index}][title]`" :id="`item_title_${index}`" x-model="item.title" class="mt-1 w-full border-neutral-300 border-1 rounded-md">
                                </div>
                                <div class="md:row-span-2">
                                    <label :for="`item_desc_${index}`" class="block text-sm font-medium">Deskripsi</label>
                                    <textarea :name="`items[${index}][description]`" :id="`item_desc_${index}`" x-model="item.description" required rows="5" class="mt-1 w-full border-neutral-300 border-1 rounded-md"></textarea>
                                </div>
                                <div class="md:col-span-2 space-y-4">
                                    <template x-if="material.type === 'Gambar'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium">Unggah Gambar (Wajib)</label>
                                            <input type="file" :name="`items[${index}][file]`" class="w-full text-sm">
                                            <label class="block text-sm font-medium mt-2">Unggah Audio (Opsional)</label>
                                            <input type="file" :name="`items[${index}][audio_file]`" class="w-full text-sm">
                                        </div>
                                    </template>
                                    <template x-if="material.type === 'Teks'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium">Unggah Audio (Opsional)</label>
                                            <input type="file" :name="`items[${index}][audio_file]`" class="w-full text-sm">
                                        </div>
                                    </template>
                                    <template x-if="material.type === 'Audio'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium">Unggah Audio</label>
                                            <input type="file" :name="`items[${index}][file]`" class="w-full text-sm">
                                        </div>
                                    </template>
                                    <template x-if="material.type === 'Video'">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium">URL Video</label>
                                            <input type="url" :name="`items[${index}][url]`" x-model="item.url" placeholder="https://youtube.com/..." class="mt-1 w-full border-neutral-300 rounded-md">
                                        </div>
                                    </template>
                                </div>
                                <div class="text-right md:col-span-2">
                                    <button type="button" @click="material.items.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus Item Ini</button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="material.items.push({title: '', description: '', url: ''})" class="mt-4 bg-green-500 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-green-600"><i class="fas fa-plus mr-2"></i>Tambah Item</button>

                    <div class="mt-8 border-t pt-5 flex justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="bg-neutral-200 text-neutral-800 py-2 px-6 rounded-lg font-semibold hover:bg-neutral-300">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg shadow font-semibold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>