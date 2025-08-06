<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Material</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
        material: { lesson_id: '', type: '', items: [{ title: '', description: [{chunk: '', translation: ''}], media_url: null }] },

        openModal(existingMaterial = null) {
            if (existingMaterial) {
                this.isEditMode = true;
                this.modalTitle = 'Edit Material';
                this.formAction = `/superadmin/materials/${existingMaterial.id}`;
                this.material = JSON.parse(JSON.stringify(existingMaterial));
            } else {
                this.isEditMode = false;
                this.modalTitle = 'Buat Material Baru';
                this.formAction = '{{ route('superadmin.materials.store') }}';
                this.material = { lesson_id: '', type: '', items: [{ title: '', description: [{chunk: '', translation: ''}] }] };
            }
            this.isModalOpen = true;
        },

        addItem() {
            this.material.items.push({title: '', description: [{chunk: '', translation: ''}], media_url: null});
        },
        removeItem(index) {
            this.material.items.splice(index, 1);
        },

        addDescription(itemIndex) {
            if (!this.material.items[itemIndex].description) {
                this.material.items[itemIndex].description = [];
            }
            this.material.items[itemIndex].description.push({ chunk: '', translation: '' });
        },
        removeDescription(itemIndex, descIndex) {
            if (this.material.items[itemIndex] && this.material.items[itemIndex].description) {
                this.material.items[itemIndex].description.splice(descIndex, 1);
            }
        }
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen Material</h1>
                        <p class="text-neutral-500">Kelola tipe dan item materi pembelajaran.</p>
                    </div>
                    <button @click="openModal()" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i> Buat Baru
                    </button>
                </header>

                @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500">
                        <thead class="text-xs text-neutral-500 uppercase bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 font-medium">Tipe</th>
                                <th class="px-6 py-3 font-medium">Lesson Induk</th>
                                <th class="px-6 py-3 font-medium">Jml. Item</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($materials as $material)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-neutral-900">{{ $material->type }}</td>
                                <td class="px-6 py-4">{{ $material->lesson->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $material->items->count() }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="openModal({{ json_encode($material) }})" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Yakin?');">
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

                    <div class="p-4 bg-neutral-50 rounded-lg border mb-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4">Informasi Material</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700">Pilih Lesson Induk</label>
                                <select name="lesson_id" x-model="material.lesson_id" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Lesson --</option>
                                    @foreach($lessons as $lesson)<option value="{{ $lesson->id }}">{{ $lesson->title }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700">Tipe Material</label>
                                <input type="text" name="type" x-model="material.type" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <h3 class="text-lg font-semibold text-neutral-800 border-b pb-2">Item Materi</h3>
                        <template x-for="(item, index) in material.items" :key="index">
                            <div class="item-group border-t pt-4">
                                <template x-if="isEditMode && item.id"><input type="hidden" :name="`items[${index}][id]`" x-model="item.id"></template>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700">Judul Item</label>
                                        <input type="text" :name="`items[${index}][title]`" x-model="item.title" class="mt-1 w-full px-3 py-2 border border-neutral-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700">File Media (Opsional)</label>
                                        <input type="file" :name="`items[${index}][media]`" accept="image/*,audio/*,video/mp4" class="w-full mt-1 text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                        <template x-if="isEditMode && item.media_url"><input type="hidden" :name="`items[${index}][existing_media_url]`" :value="item.media_url"></template>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 border bg-gray-50 rounded-md">
                                    <label class="block text-sm font-medium text-neutral-700 mb-2">Deskripsi (Penggalan Teks & Terjemahan)</label>
                                    <template x-if="!item.description || item.description.length === 0">
                                        <div x-init="item.description = [{chunk: '', translation: ''}]"></div>
                                    </template>
                                    <template x-for="(desc, descIndex) in item.description" :key="descIndex">
                                        <div class="grid grid-cols-2 gap-2 mb-2 items-center">
                                            <input type="text" :name="`items[${index}][description][${descIndex}][chunk]`" x-model="desc.chunk" placeholder="Penggalan Teks (EN)" class="px-3 py-2 border border-neutral-300 rounded-md">
                                            <div class="flex items-center gap-2">
                                                <input type="text" :name="`items[${index}][description][${descIndex}][translation]`" x-model="desc.translation" placeholder="Terjemahan (ID)" class="flex-1 px-3 py-2 border border-neutral-300 rounded-md">
                                                <button type="button" @click="removeDescription(index, descIndex)" class="text-red-500 font-bold">&times;</button>
                                            </div>
                                        </div>
                                    </template>
                                    <button type="button" @click="addDescription(index)" class="text-xs text-indigo-600">+ Tambah Penggalan</button>
                                </div>

                                <div class="text-right mt-2"><button type="button" @click="removeItem(index)" class="text-sm font-medium text-red-600 hover:text-red-800">Hapus Item</button></div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addItem()" class="mt-4 bg-green-500 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-green-600"><i class="fas fa-plus mr-2"></i>Tambah Item</button>

                    <div class="mt-8 border-t pt-5 flex justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="bg-neutral-200 py-2 px-6 rounded-lg">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>