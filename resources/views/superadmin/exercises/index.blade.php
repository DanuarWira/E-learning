<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Exercises</title>
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
        lessons: {{ json_encode($lessons) }},
        exercise: {},

        openModal(existingExercise = null) {
            if (existingExercise) {
                this.isEditMode = true;
                this.modalTitle = 'Edit Latihan';
                this.formAction = `/superadmin/exercises/${existingExercise.id}`;
                this.exercise = JSON.parse(JSON.stringify(existingExercise)); 
                if (typeof this.exercise.content !== 'object' || this.exercise.content === null) {
                    this.resetContent();
                }
            } else {
                this.isEditMode = false;
                this.modalTitle = 'Buat Latihan Baru';
                this.formAction = '{{ route('superadmin.exercises.store') }}';
                this.exercise = { lesson_id: '', title: '', type: 'matching_game', content: { pairs: [{question: '', answer: ''}] } };
            }
            this.isModalOpen = true;
        },

        resetContent() {
            const type = this.exercise.type;
            if (type === 'matching_game') this.exercise.content = { pairs: [{question: '', answer: ''}] };
            else if (type === 'spelling_quiz') this.exercise.content = { correct_answer: '' };
            else if (type === 'sentence_scramble') this.exercise.content = { sentence: '' };
            else if (type === 'fill_in_the_blank') this.exercise.content = { sentence_template: '___', correct_answer: '' };
            else if (type === 'fill_multiple_blanks') this.exercise.content = { sentence_parts: [], correct_answers: [] };
            else if (type === 'multiple_choice_quiz') this.exercise.content = { question_text: '', options: [], correct_answer: '' };
            else this.exercise.content = {};
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
                            @forelse($exercises as $exercise)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $exercise->title }}</td>
                                <td class="px-6 py-4"><span class="font-mono text-xs bg-neutral-200 text-neutral-700 px-2 py-1 rounded">{{ $exercise->type }}</span></td>
                                <td class="px-6 py-4">{{ $exercise->lesson->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="openModal({{ json_encode($exercise) }})" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.exercises.destroy', $exercise) }}" method="POST" onsubmit="return confirm('Yakin?');">
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

                <form :action="formAction" method="POST" class="flex-1 overflow-y-auto pr-2">
                    @csrf
                    <template x-if="isEditMode">@method('PUT')</template>

                    <div class="space-y-6">
                        <!-- Info Dasar -->
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
                            <select name="type" x-model="exercise.type" @change="resetContent()" :disabled="isEditMode" required class="mt-1 block w-full border-neutral-300 rounded-md disabled:bg-neutral-100">
                                <option value="matching_game">Matching Game</option>
                                <option value="spelling_quiz">Spelling Quiz</option>
                                <option value="fill_in_the_blank">Fill in the Blank</option>
                                <option value="fill_multiple_blanks">Fill Multiple Blanks</option>
                                <option value="sentence_scramble">Sentence Scramble</option>
                                <option value="multiple_choice_quiz">Multiple Choice Quiz</option>
                            </select>
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="text-lg font-medium text-neutral-800 mb-2">Konten Latihan</h3>

                            <div x-show="exercise.type === 'matching_game'" class="space-y-2">
                                <template x-for="(pair, index) in exercise.content.pairs" :key="index">
                                    <div class="flex gap-2 items-center"><input type="text" :name="`content[pairs][${index}][question]`" x-model="pair.question" placeholder="Question" class="flex-1 rounded-md border-1 border-neutral-300"><input type="text" :name="`content[pairs][${index}][answer]`" x-model="pair.answer" placeholder="Answer" class="flex-1 rounded-md"><button type="button" @click="exercise.content.pairs.splice(index, 1)" class="text-red-500">&times;</button></div>
                                </template>
                                <button type="button" @click="exercise.content.pairs.push({question: '', answer: ''})" class="text-sm text-indigo-600">+ Tambah Pasangan</button>
                            </div>

                            <div x-show="exercise.type === 'spelling_quiz'" class="space-y-2"><label class="block text-sm">Jawaban Benar</label><input type="text" name="content[correct_answer]" x-model="exercise.content.correct_answer" class="w-full rounded-md border-1 border-neutral-300"></div>
                            <div x-show="exercise.type === 'sentence_scramble'" class="space-y-2"><label class="block text-sm">Kalimat Benar</label><input type="text" name="content[sentence]" x-model="exercise.content.sentence" class="w-full rounded-md border-1 border-neutral-300"></div>
                            <div x-show="exercise.type === 'fill_in_the_blank'" class="space-y-2"><label class="block text-sm">Template Kalimat (gunakan ___)</label><input type="text" name="content[sentence_template]" x-model="exercise.content.sentence_template" class="w-full rounded-md border-1 border-neutral-300"><label class="block text-sm">Jawaban Benar</label><input type="text" name="content[correct_answer]" x-model="exercise.content.correct_answer" class="w-full rounded-md"></div>
                            <div x-show="exercise.type === 'fill_multiple_blanks'" class="space-y-4">
                                <p class="text-sm text-neutral-500">Buat kalimat dengan menambahkan bagian dan jawaban. Contoh: ["Kalimat awal ", " kalimat akhir."], ["jawaban_pertama", "jawaban_kedua"]</p>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold">Bagian Kalimat</label>
                                    <template x-for="(part, index) in exercise.content.sentence_parts" :key="index">
                                        <div class="flex items-center gap-2">
                                            <input type="text" :name="`content[sentence_parts][${index}]`" x-model="exercise.content.sentence_parts[index]" :placeholder="`Bagian ${index + 1}`" class="flex-1 rounded-md">
                                            <button type="button" @click="exercise.content.sentence_parts.splice(index, 1)" class="text-red-500">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="exercise.content.sentence_parts.push('')" class="text-sm text-indigo-600">+ Tambah Bagian Kalimat</button>
                                </div>
                                <div class="space-y-2 border-t pt-2">
                                    <label class="block text-sm font-semibold">Jawaban Benar (sesuai urutan)</label>
                                    <template x-for="(answer, index) in exercise.content.correct_answers" :key="index">
                                        <div class="flex items-center gap-2">
                                            <input type="text" :name="`content[correct_answers][${index}]`" x-model="exercise.content.correct_answers[index]" :placeholder="`Jawaban untuk ___ ke-${index + 1}`" class="flex-1 rounded-md">
                                            <button type="button" @click="exercise.content.correct_answers.splice(index, 1)" class="text-red-500">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="exercise.content.correct_answers.push('')" class="text-sm text-indigo-600">+ Tambah Jawaban</button>
                                </div>
                            </div>
                            <div x-show="exercise.type === 'multiple_choice_quiz'" class="space-y-2"><label class="block text-sm">Pertanyaan</label><input type="text" name="content[question_text]" x-model="exercise.content.question_text" class="w-full rounded-md"><label class="block text-sm">Pilihan (pisahkan dengan koma)</label><input type="text" name="content[options]" :value="exercise.content.options?.join(',')" @input="exercise.content.options = $event.target.value.split(',').map(s => s.trim())" class="w-full rounded-md border-1 border-neutral-300"><label class="block text-sm">Jawaban Benar</label><input type="text" name="content[correct_answer]" x-model="exercise.content.correct_answer" class="w-full rounded-md"></div>
                        </div>
                    </div>
                    <div class="mt-8 pt-5 flex justify-end gap-3"><button type="button" @click="isModalOpen = false" class="bg-neutral-200 text-neutral-800 py-2 px-6 rounded-lg font-semibold hover:bg-neutral-300">Batal</button><button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg shadow font-semibold hover:bg-indigo-700">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>