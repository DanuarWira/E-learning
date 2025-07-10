<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Vocabulary</title>
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
        vocab: { lesson_id: '', category: '', items: [{ term: '', details: '', media_url: null }] }
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen Vocabulary</h1>
                        <p class="text-neutral-500">Kelola kategori dan item kosakata.</p>
                    </div>
                    <button @click="isModalOpen = true; isEditMode = false; modalTitle = 'Buat Vocabulary Baru'; formAction = '{{ route('superadmin.vocabularies.store') }}'; vocab = { lesson_id: '', category: '', items: [{ term: '', details: '' }] };" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
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
                                <th class="px-6 py-3 font-medium">Kategori</th>
                                <th class="px-6 py-3 font-medium">Lesson Induk</th>
                                <th class="px-6 py-3 font-medium">Jml. Item</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($vocabularies as $vocabulary)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-neutral-900">{{ $vocabulary->category }}</td>
                                <td class="px-6 py-4">{{ $vocabulary->lesson->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $vocabulary->items->count() }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="isModalOpen = true; isEditMode = true; modalTitle = 'Edit Vocabulary'; formAction = '{{ route('superadmin.vocabularies.update', $vocabulary) }}'; vocab = {{ json_encode($vocabulary) }};" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.vocabularies.destroy', $vocabulary) }}" method="POST" onsubmit="return confirm('Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">Belum ada vocabulary.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">{{ $vocabularies->links('vendor.pagination.tailwind') }}</div>
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

                    <!-- Bagian Informasi Utama -->
                    <div class="p-4 bg-neutral-50 rounded-lg border mb-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-4">Informasi Kategori</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="lesson_id" class="block text-sm font-medium text-neutral-700">Pilih Lesson Induk</label>
                                <select name="lesson_id" id="lesson_id" x-model="vocab.lesson_id" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Lesson --</option>
                                    @foreach($lessons as $lesson)<option :value="{{ $lesson->id }}">{{ $lesson->title }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-neutral-700">Nama Kategori</label>
                                <input type="text" name="category" id="category" x-model="vocab.category" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Item-item -->
                    <div id="items-container" class="space-y-4">
                        <h3 class="text-lg font-semibold text-neutral-800 border-b pb-2">Item Kosakata</h3>
                        <template x-for="(item, index) in vocab.items" :key="index">
                            <div class="item-group grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 border-t pt-4">
                                <template x-if="isEditMode"><input type="hidden" :name="`items[${index}][id]`" x-model="item.id"></template>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700">Term</label>
                                    <input type="text" :name="`items[${index}][term]`" x-model="item.term" required class="mt-1 w-full px-3 py-2 border border-neutral-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700">Details</label>
                                    <input type="text" :name="`items[${index}][details]`" x-model="item.details" class="mt-1 w-full px-3 py-2 border border-neutral-300 rounded-md">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-neutral-700">File Audio/Video (Opsional)</label>
                                    <template x-if="isEditMode && item.media_url">
                                        <p class="text-xs text-neutral-500 mb-1">File saat ini: <a :href="item.media_url" target="_blank" class="text-indigo-600" x-text="item.media_url.split('/').pop()"></a></p>
                                    </template>
                                    <input type="file" :name="`items[${index}][media]`" class="w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    <template x-if="isEditMode && item.media_url"><input type="hidden" :name="`items[${index}][existing_media_url]`" :value="item.media_url"></template>
                                </div>
                                <div class="text-right md:col-span-2"><button type="button" @click="vocab.items.splice(index, 1)" class="text-sm font-medium text-red-600 hover:text-red-800">Hapus Item</button></div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="vocab.items.push({term: '', details: '', media_url: null})" class="mt-4 bg-green-500 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-green-600"><i class="fas fa-plus mr-2"></i>Tambah Item</button>

                    <div class="mt-8 border-t pt-5 flex justify-end gap-3"><button type="button" @click="isModalOpen = false" class="bg-neutral-200 py-2 px-6 rounded-lg">Batal</button><button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>