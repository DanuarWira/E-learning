<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Exercises</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .input-file {
            font-size: 0.875rem;
            color: #64748b;
        }

        .input-file::file-selector-button {
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border-width: 0;
            font-size: 0.875rem;
            font-weight: 600;
            background-color: #f1f5f9;
            color: #4338ca;
        }

        .input-file::file-selector-button:hover {
            background-color: #e2e8f0;
        }
    </style>
</head>

<body class="bg-neutral-100 font-sans">
    <div x-data="{
        isModalOpen: false, 
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        lessons: {{ json_encode($lessons) }},
        exercise: {},

        openModal(existingExercise = null) {
            if (existingExercise) {
                this.isEditMode = true;
                this.modalTitle = 'Edit Latihan';
                this.formAction = `/superadmin/exercises/${existingExercise.id}`;
                this.exercise = {
                    id: existingExercise.id,
                    lesson_id: existingExercise.lesson_id,
                    title: existingExercise.title,
                    type: existingExercise.exerciseable_type,
                    content: JSON.parse(JSON.stringify(existingExercise.exerciseable))
                };
                
                if (this.exercise.content && this.exercise.content.options) {
                    const correctIndex = this.exercise.content.options.indexOf(this.exercise.content.correct_answer);
                    this.exercise.content.correct_answer = correctIndex >= 0 ? correctIndex : 0;
                    this.exercise.content.options = this.exercise.content.options.map(opt => ({ value: opt }));
                }
                if (this.exercise.content && this.exercise.content.pairs) {
                    this.exercise.content.pairs = this.exercise.content.pairs.map(p => ({ item1: { value: p.item1 }, item2: { value: p.item2 } }));
                }

            } else {
                this.isEditMode = false;
                this.modalTitle = 'Buat Latihan Baru';
                this.formAction = '{{ route("superadmin.exercises.store") }}';
                this.exercise = { 
                    lesson_id: '', 
                    title: '', 
                    type: 'multiple_choice_quiz',
                    content: { question_text: '', options: [{value: ''}], correct_answer: 0 } 
                };
            }
            this.isModalOpen = true;
        },

        resetContentOnTypeChange() {
            const type = this.exercise.type;
            if (['multiple_choice_quiz', 'translation_match', 'fill_with_options'].includes(type)) {
                this.exercise.content = { question_text: '', question_media_url: '', options: [{value: ''}], correct_answer: 0 };
            }
            else if (type === 'matching_game') { this.exercise.content = { instruction: '', pairs: [{item1: '', item2: ''}] }; }
            else if (type === 'silent_letter_hunt') { this.exercise.content = { sentence: '', words: [{word: '', silent_letter_index: 0}] }; }
            else if (type === 'spelling_quiz') { this.exercise.content = { audio_url: '', correct_answer: '' }; }
            else if (type === 'sound_sorting') { this.exercise.content = { categories: [{name: '', id: ''}], words: [{word: '', category_id: ''}] }; }
            else if (type === 'sentence_scramble') { this.exercise.content = { sentence: '' }; }
            else if (type === 'fill_multiple_blanks') { this.exercise.content = { sentence_parts: [''], correct_answers: [''] }; }
            else if (type === 'sequencing') { this.exercise.content = { steps: [''] }; }
            else if (type === 'speaking_quiz') { this.exercise.content = { media_url: '', media_type: '', prompt_text: '' }; }
            else { this.exercise.content = {}; }
        },
        addItem(key) {
            if (!this.exercise.content[key]) {
                this.exercise.content[key] = [];
            }
            
            if (key === 'options') {
                this.exercise.content.options.push({ value: '' });
            } else if (key === 'pairs') {
                this.exercise.content.pairs.push({ item1: '', item2: '' });
            } else if (key === 'words') {
                this.exercise.content.words.push({ word: '', silent_letter_index: 0 });
            } else if (key === 'ss_words') {
                this.exercise.content.words.push({ word: '', category_id: '' });
            } else if (key === 'ss_categories') {
                this.exercise.content.categories.push({ name: '', id: '' });
            } else {
                this.exercise.content[key].push('');
            }
        },
        removeItem(key, index) {
            if (this.exercise.content[key]) {
                this.exercise.content[key].splice(index, 1);
            }
        }
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen Latihan</h1>
                        <p class="text-neutral-500">Kelola semua jenis latihan interaktif.</p>
                    </div>
                    <button @click="openModal()" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i> Buat Latihan Baru
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
                                <th class="px-6 py-3">Judul Latihan</th>
                                <th class="px-6 py-3">Tipe</th>
                                <th class="px-6 py-3">Lesson Induk</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($exercises->load('lesson', 'exerciseable') as $exercise)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $exercise->title }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs bg-neutral-200 text-neutral-700 px-2 py-1 rounded">
                                        {{ Str::title(str_replace('_', ' ', $exercise->exerciseable_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $exercise->lesson->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="openModal({{ json_encode($exercise) }})" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.exercises.destroy', $exercise) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus latihan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">Belum ada latihan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">{{ $exercises->links('vendor.pagination.tailwind') }}</div>
                </div>
            </main>
        </div>

        <!-- Modal Form -->
        <div x-show="isModalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/20">
            <div @click.away="isModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-8 m-4 max-h-[90vh] flex flex-col">
                <h2 class="text-2xl font-bold text-neutral-800 mb-6" x-text="modalTitle"></h2>

                <form :action="formAction" method="POST" class="flex-1 overflow-y-auto pr-2" enctype="multipart/form-data">
                    @csrf
                    <template x-if=" isEditMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium">Pilih Lesson Induk</label>
                            <select name="lesson_id" x-model="exercise.lesson_id" required class="mt-1 block w-full border-neutral-300 rounded-md">
                                <option value="">-- Pilih Lesson --</option>
                                <template x-for="lesson in lessons" :key="lesson.id">
                                    <option :value="lesson.id" x-text="lesson.title"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Judul Latihan</label>
                            <input type="text" name="title" x-model="exercise.title" required class="mt-1 block w-full border-neutral-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Tipe Latihan</label>
                            <select name="type" x-model="exercise.type" @change="resetContentOnTypeChange()" :disabled="isEditMode" required class="mt-1 block w-full border-neutral-300 rounded-md disabled:bg-neutral-100">
                                <option value="multiple_choice_quiz">Multiple Choice Quiz</option>
                                <option value="matching_game">Matching Game</option>
                                <option value="pronunciation_drill">Pronunciation Drill</option>
                                <option value="translation_match">Translation Match</option>
                                <option value="silent_letter_hunt">Silent Letter Hunt</option>
                                <option value="spelling_quiz">Spelling Quiz</option>
                                <option value="sound_sorting">Sound Sorting</option>
                                <option value="sentence_scramble">Sentence Scramble</option>
                                <option value="fill_multiple_blanks">Fill Multiple Blanks</option>
                                <option value="sequencing">Sequencing</option>
                                <option value="fill_with_options">Fill With Options</option>
                                <option value="speaking_quiz">Speaking Quiz</option>
                            </select>
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="text-lg font-medium text-neutral-800 mb-2">Konten Latihan</h3>

                            <div x-show="['multiple_choice_quiz', 'fill_with_options', 'translation_match'].includes(exercise.type)" class="space-y-4">
                                <div x-show="exercise.type === 'multiple_choice_quiz'">
                                    <label class="block text-sm font-medium">Teks Pertanyaan</label>
                                    <textarea name="content[question_text]" x-model="exercise.content.question_text" class="mt-1 block w-full border-neutral-300 rounded-md"></textarea>
                                </div>
                                <label class="block text-xs font-medium text-gray-600 mt-3">Atau Media Pertanyaan (Gambar/Audio/Video)</label>
                                <input type="file" name="content[question_media]" accept="image/*,audio/*,video/mp4" class="input-file w-full mt-1">
                                <template x-if="isEditMode && exercise.content.question_media_url">
                                    <div class="mt-2">
                                        <template x-if="exercise.content.question_media_type === 'image'">
                                            <img :src="`${window.location.origin}${exercise.content.question_media_url}`" class="w-24 h-24 object-cover rounded">
                                        </template>
                                        <template x-if="exercise.content.question_media_type === 'audio'">
                                            <audio :src="`${window.location.origin}${exercise.content.question_media_url}`" controls class="w-full"></audio>
                                        </template>
                                        <template x-if="exercise.content.question_media_type === 'video'">
                                            <video :src="`${window.location.origin}${exercise.content.question_media_url}`" controls class="w-full rounded"></video>
                                        </template>
                                    </div>
                                </template>
                                <div x-show="exercise.type === 'translation_match'">
                                    <label class="block text-sm font-medium">Kata/Frasa Pertanyaan</label>
                                    <input type="text" name="content[question_word]" x-model="exercise.content.question_word" class="mt-1 block w-full border-neutral-300 rounded-md">
                                </div>
                                <div x-show="exercise.type === 'fill_with_options'">
                                    <label class="block text-sm font-medium mb-1">Bagian Kalimat</label>
                                    <input type="text" name="content[sentence_parts][]" x-model="exercise.content.sentence_parts[0]" placeholder="Bagian sebelum jawaban" class="w-full rounded-md border-neutral-300 mb-2">
                                    <input type="text" name="content[sentence_parts][]" x-model="exercise.content.sentence_parts[1]" placeholder="Bagian setelah jawaban" class="w-full rounded-md border-neutral-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Opsi Jawaban (Pilih satu sebagai jawaban benar)</label>
                                    <template x-for="(option, index) in exercise.content.options" :key="index">
                                        <div class="flex items-start gap-3 mb-2 p-3 border rounded-md">
                                            <input type="radio" name="content[correct_answer]" :value="index" x-model.number="exercise.content.correct_answer" class="mt-5">
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-500">Opsi Teks</p>
                                                <input type="text" :name="`content[options][${index}][text]`" :value="option.value && !String(option.value).startsWith('/storage/') ? option.value : ''" placeholder="Teks Opsi" class="w-full rounded-md border-neutral-300">

                                                <p class="text-xs text-gray-500 mt-2">Atau Opsi Gambar</p>
                                                <input type="file" :name="`content[options][${index}][image]`" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">

                                                <div x-show="isEditMode && option.value && String(option.value).startsWith('/storage/')">
                                                    <img :src="`${window.location.origin}${option.value}`" class="w-20 h-20 object-cover mt-2 rounded">
                                                </div>
                                            </div>
                                            <button type="button" @click="removeItem('options', index)" class="text-red-500 font-bold mt-4">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('options')" class="text-sm text-indigo-600">+ Tambah Opsi</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'matching_game'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">Instruksi</label>
                                    <textarea name="content[instruction]" x-model="exercise.content.instruction" class="mt-1 block w-full border-neutral-300 rounded-md"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Pasangan</label>
                                    <template x-for="(pair, index) in exercise.content.pairs" :key="index">
                                        <div class="flex items-start gap-2 mb-2 p-3 border rounded-md">
                                            <!-- Item 1 -->
                                            <div class="flex-1 space-y-2">
                                                <p class="text-sm font-semibold">Item 1</p>
                                                <input type="text" :name="`content[pairs][${index}][item1][text]`" :value="pair.item1.value && !String(pair.item1.value).startsWith('/storage/') ? pair.item1.value : ''" placeholder="Teks Item 1" class="w-full rounded-md border-neutral-300">
                                                <p class="text-xs text-gray-500">atau Gambar</p>
                                                <input type="file" :name="`content[pairs][${index}][item1][image]`" accept="image/*" class="input-file mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                                <p class="text-xs text-gray-500">atau Audio</p>
                                                <input type="file" :name="`content[pairs][${index}][item1][audio]`" accept="audio/*" class="input-file mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                                <div x-show="isEditMode && pair.item1.value && String(pair.item1.value).startsWith('/storage/')">
                                                    <img x-show="String(pair.item1.value).match(/\.(jpeg|jpg|gif|png)$/)" :src="`${window.location.origin}${pair.item1.value}`" class="w-20 h-20 object-cover mt-2 rounded">
                                                    <audio x-show="String(pair.item1.value).match(/\.(mp3|wav|ogg)$/)" :src="`${window.location.origin}${pair.item1.value}`" controls class="w-full mt-2"></audio>
                                                </div>
                                            </div>
                                            <span class="text-neutral-400 mt-8 font-bold">=</span>
                                            <!-- Item 2 -->
                                            <div class="flex-1 space-y-2">
                                                <p class="text-sm font-semibold">Item 2</p>
                                                <input type="text" :name="`content[pairs][${index}][item2][text]`" :value="pair.item2.value && !String(pair.item2.value).startsWith('/storage/') ? pair.item2.value : ''" placeholder="Teks Item 2" class="w-full rounded-md border-neutral-300">
                                                <p class="text-xs text-gray-500">atau Gambar</p>
                                                <input type="file" :name="`content[pairs][${index}][item2][image]`" accept="image/*" class="input-file mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                                <p class="text-xs text-gray-500">atau Audio</p>
                                                <input type="file" :name="`content[pairs][${index}][item2][audio]`" accept="audio/*" class="input-file mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                                <div x-show="isEditMode && pair.item2.value && String(pair.item2.value).startsWith('/storage/')">
                                                    <img x-show="String(pair.item2.value).match(/\.(jpeg|jpg|gif|png)$/)" :src="`${window.location.origin}${pair.item2.value}`" class="w-20 h-20 object-cover mt-2 rounded">
                                                    <audio x-show="String(pair.item2.value).match(/\.(mp3|wav|ogg)$/)" :src="`${window.location.origin}${pair.item2.value}`" controls class="w-full mt-2"></audio>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeItem('pairs', index)" class="text-red-500 font-bold mt-8">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('pairs')" class="text-sm text-indigo-600">+ Tambah Pasangan</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'silent_letter_hunt'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">Kalimat Lengkap</label>
                                    <textarea name="content[sentence]" x-model="exercise.content.sentence" class="mt-1 block w-full border-neutral-300 rounded-md" placeholder="The knight knows how to write."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Kata dengan Silent Letter</label>
                                    <template x-for="(word, index) in exercise.content.words" :key="index">
                                        <div class="flex items-center gap-2 mb-2 p-2 border rounded-md">
                                            <input type="text" :name="`content[words][${index}][word]`" x-model="word.word" placeholder="Kata (e.g., knight)" class="flex-1 rounded-md border-neutral-300">
                                            <input type="number" :name="`content[words][${index}][silent_letter_index]`" x-model.number="word.silent_letter_index" placeholder="Indeks (e.g., 0)" class="w-24 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('words', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('words')" class="text-sm text-indigo-600">+ Tambah Kata</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'spelling_quiz'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">File Audio</label>
                                    <input type="file" name="content[audio_file]" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    <p class="text-xs text-gray-500 mt-1" x-show="isEditMode && exercise.content.audio_url">Kosongkan jika tidak ingin mengubah audio yang ada: <a :href="exercise.content.audio_url" target="_blank" class="text-blue-500">Lihat Audio</a></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Jawaban Benar (Ejaan)</label>
                                    <input type="text" name="content[correct_answer]" x-model="exercise.content.correct_answer" class="mt-1 block w-full border-neutral-300 rounded-md" placeholder="Contoh: reservation">
                                </div>
                            </div>

                            <div x-show="exercise.type === 'sound_sorting'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Kategori Suara</label>
                                    <template x-for="(category, index) in exercise.content.categories" :key="index">
                                        <div class="flex items-center gap-2 mb-2 p-2 border rounded-md">
                                            <input type="text" :name="`content[categories][${index}][name]`" x-model="category.name" placeholder="Nama Kategori (e.g., /r/ sound)" class="flex-1 rounded-md border-neutral-300">
                                            <input type="text" :name="`content[categories][${index}][id]`" x-model="category.id" placeholder="ID Unik (e.g., r_sound)" class="flex-1 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('categories', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('ss_categories')" class="text-sm text-indigo-600">+ Tambah Kategori</button>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Kata</label>
                                    <template x-for="(word, index) in exercise.content.words" :key="index">
                                        <div class="flex items-center gap-2 mb-2 p-2 border rounded-md">
                                            <input type="text" :name="`content[words][${index}][word]`" x-model="word.word" placeholder="Kata (e.g., room)" class="flex-1 rounded-md border-neutral-300">
                                            <input type="text" :name="`content[words][${index}][category_id]`" x-model="word.category_id" placeholder="ID Kategori (e.g., r_sound)" class="flex-1 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('ss_words', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('ss_words')" class="text-sm text-indigo-600">+ Tambah Kata</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'sentence_scramble'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">Kalimat Benar</label>
                                    <textarea name="content[sentence]" x-model="exercise.content.sentence" class="mt-1 block w-full border-neutral-300 rounded-md" placeholder="Hi, welcome to our hotel!"></textarea>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'fill_multiple_blanks'" class="space-y-4">
                                <p class="text-sm text-neutral-500">Buat kalimat dengan bagian dan jawaban. Contoh: ["Yesterday, the guest ", " and ", " a key."], ["arrived", "took"]</p>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Bagian Kalimat</label>
                                    <template x-for="(part, index) in exercise.content.sentence_parts" :key="index">
                                        <div class="flex items-center gap-2 mb-2">
                                            <input type="text" :name="`content[sentence_parts][${index}]`" x-model="exercise.content.sentence_parts[index]" :placeholder="`Bagian ${index + 1}`" class="flex-1 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('sentence_parts', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('sentence_parts')" class="text-sm text-indigo-600">+ Tambah Bagian</button>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Jawaban Benar (sesuai urutan)</label>
                                    <template x-for="(answer, index) in exercise.content.correct_answers" :key="index">
                                        <div class="flex items-center gap-2 mb-2">
                                            <input type="text" :name="`content[correct_answers][${index}]`" x-model="exercise.content.correct_answers[index]" :placeholder="`Jawaban ke-${index + 1}`" class="flex-1 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('correct_answers', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('correct_answers')" class="text-sm text-indigo-600">+ Tambah Jawaban</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'sequencing'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Langkah-langkah (sesuai urutan)</label>
                                    <template x-for="(step, index) in exercise.content.steps" :key="index">
                                        <div class="flex items-center gap-2 mb-2">
                                            <input type="text" :name="`content[steps][${index}]`" x-model="exercise.content.steps[index]" :placeholder="`Langkah ${index + 1}`" class="flex-1 rounded-md border-neutral-300">
                                            <button type="button" @click="removeItem('steps', index)" class="text-red-500 font-bold">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem('steps')" class="text-sm text-indigo-600">+ Tambah Langkah</button>
                                </div>
                            </div>

                            <div x-show="exercise.type === 'speaking_quiz'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">Gambar (Opsional)</label>
                                    <input type="file" name="content[prompt_image]" accept="image/*" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    <template x-if="isEditMode && exercise.content.media_type === 'image'">
                                        <img :src="`${window.location.origin}${exercise.content.media_url}`" class="w-24 h-24 object-cover mt-2 rounded">
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Audio (Opsional)</label>
                                    <input type="file" name="content[prompt_audio]" accept="audio/*" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    <template x-if="isEditMode && exercise.content.media_type === 'audio'">
                                        <audio :src="`${window.location.origin}${exercise.content.media_url}`" controls class="w-full mt-2"></audio>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Hint (Opsional)</label>
                                    <input type="text" name="content[hints]" x-model="exercise.content.hints" class="mt-1 block w-full border-neutral-300 rounded-md" placeholder="Tuliskan hint">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Jawaban Benar (Teks)</label>
                                    <input type="text" name="content[prompt_text]" x-model="exercise.content.prompt_text" class="mt-1 block w-full border-neutral-300 rounded-md" placeholder="Tulis jawaban yang diharapkan">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="mt-8 pt-5 flex justify-end gap-3 border-t">
                        <button type="button" @click="isModalOpen = false" class="bg-neutral-200 text-neutral-800 py-2 px-6 rounded-lg font-semibold hover:bg-neutral-300">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg shadow font-semibold hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>