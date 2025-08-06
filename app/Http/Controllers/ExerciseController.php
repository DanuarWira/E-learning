<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ExerciseController extends Controller
{
    /**
     * Menampilkan satu halaman latihan interaktif.
     * Parameter $lesson diambil dari URL, meskipun kita bisa mendapatkannya dari $exercise->lesson.
     */
    public function show(Lesson $lesson, Exercise $exercise): View
    {
        if ($exercise->lesson_id !== $lesson->id) {
            abort(404);
        }

        return view('exercise', compact('exercise'));
    }

    public function index(): View
    {
        $exercises = Exercise::with('lesson', 'exerciseable')->latest()->paginate(10);
        $lessons = Lesson::orderBy('title')->get();
        return view('superadmin.exercises.index', compact('exercises', 'lessons'));
    }

    public function store(Request $request)
    {
        // Membersihkan input file yang kosong sebelum validasi
        $content = $request->input('content', []);
        if (isset($content['options'])) {
            foreach ($content['options'] as $index => $option) {
                if (!$request->hasFile("content.options.{$index}.image")) {
                    $content['options'][$index]['image'] = null;
                }
            }
        }
        $request->merge(['content' => $content]);

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|array',
            // Validasi untuk semua kemungkinan field di dalam 'content'
            'content.question_text' => 'nullable|string',
            'content.question_word' => 'nullable|string',
            'content.correct_answer' => 'nullable|integer', // Menerima index dari radio button
            'content.prompt_text' => 'nullable|string',
            'content.sentence' => 'nullable|string',
            'content.instruction' => 'nullable|string',
            'content.sentence_parts' => 'nullable|array',
            'content.correct_answers' => 'nullable|array',
            'content.words' => 'nullable|array',
            'content.categories' => 'nullable|array',
            'content.steps' => 'nullable|array',
            'content.options' => 'nullable|array',
            'content.options.*.text' => 'nullable|string',
            'content.options.*.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $modelClass = Relation::getMorphedModel($validated['type']);
            $contentData = $validated['content'];
            $finalOptions = [];

            if (isset($contentData['options'])) {
                foreach ($contentData['options'] as $index => $option) {
                    if ($request->hasFile("content.options.{$index}.image")) {
                        $path = $request->file("content.options.{$index}.image")->store('options', 'public');
                        $finalOptions[] = Storage::url($path);
                    } else {
                        $finalOptions[] = $option['text'] ?? '';
                    }
                }
                $contentData['options'] = $finalOptions;
            }

            // --- INI BAGIAN KUNCINYA ---
            // Mengubah 'correct_answer' yang berisi INDEX menjadi NILAI JAWABAN
            if (isset($contentData['correct_answer'])) {
                $correctIndex = (int)$contentData['correct_answer'];
                if (isset($finalOptions[$correctIndex])) {
                    // Nilai 'correct_answer' ditimpa dengan nilai dari array $finalOptions
                    $contentData['correct_answer'] = $finalOptions[$correctIndex];
                } else {
                    $contentData['correct_answer'] = null; // Default jika index tidak valid
                }
            }

            if ($modelClass && class_exists($modelClass)) {
                $exerciseDetail = $modelClass::create($contentData);
                $exercise = new Exercise([
                    'lesson_id' => $validated['lesson_id'],
                    'title' => $validated['title'],
                ]);
                $exerciseDetail->exercise()->save($exercise);
            }
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil dibuat.');
    }

    /**
     * Memperbarui exercise dari form modal.
     */
    public function update(Request $request, Exercise $exercise)
    {
        // Membersihkan input file yang kosong sebelum validasi
        $content = $request->input('content', []);
        if (isset($content['options'])) {
            foreach ($content['options'] as $index => $option) {
                if (!$request->hasFile("content.options.{$index}.image")) {
                    $content['options'][$index]['image'] = null;
                }
            }
        }
        $request->merge(['content' => $content]);

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
            // --- PERBAIKAN: Tambahkan validasi untuk semua field konten ---
            'content.question_text' => 'nullable|string',
            'content.question_word' => 'nullable|string',
            'content.correct_answer' => 'nullable|integer',
            'content.prompt_text' => 'nullable|string',
            'content.sentence' => 'nullable|string',
            'content.instruction' => 'nullable|string',
            'content.sentence_parts' => 'nullable|array',
            'content.correct_answers' => 'nullable|array',
            'content.words' => 'nullable|array',
            'content.categories' => 'nullable|array',
            'content.steps' => 'nullable|array',
            'content.options' => 'nullable|array',
            'content.options.*.text' => 'nullable|string',
            'content.options.*.image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'content.audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg'],
        ]);

        DB::transaction(function () use ($validated, $request, $exercise) {
            $exercise->update([
                'lesson_id' => $validated['lesson_id'],
                'title' => $validated['title'],
            ]);

            $contentData = $validated['content'];
            $exerciseDetail = $exercise->exerciseable;
            $finalOptions = [];

            if ($exerciseDetail && isset($contentData['options'])) {
                $oldOptions = $exerciseDetail->options ?? [];
                foreach ($contentData['options'] as $index => $option) {
                    if ($request->hasFile("content.options.{$index}.image")) {
                        if (isset($oldOptions[$index]) && Str::startsWith($oldOptions[$index], '/storage')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptions[$index]));
                        }
                        $path = $request->file("content.options.{$index}.image")->store('options', 'public');
                        $finalOptions[] = Storage::url($path);
                    } else {
                        $textValue = $option['text'] ?? null;
                        if ($textValue === null && isset($oldOptions[$index]) && Str::startsWith($oldOptions[$index], '/storage')) {
                            $finalOptions[] = $oldOptions[$index];
                        } else {
                            $finalOptions[] = $textValue ?? '';
                        }
                    }
                }
                $contentData['options'] = $finalOptions;
            }

            if (isset($contentData['correct_answer'])) {
                $correctIndex = (int)$contentData['correct_answer'];
                $optionsToUse = !empty($finalOptions) ? $finalOptions : ($exerciseDetail->options ?? []);
                if (isset($optionsToUse[$correctIndex])) {
                    $contentData['correct_answer'] = $optionsToUse[$correctIndex];
                } else {
                    $contentData['correct_answer'] = null;
                }
            }

            if ($exerciseDetail) {
                // ... logika update audio ...
                if ($exercise->exerciseable_type === 'spelling_quiz' && $request->hasFile('content.audio_file')) {
                    if ($exerciseDetail->audio_url) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $exerciseDetail->audio_url));
                    }
                    $path = $request->file('content.audio_file')->store('audio', 'public');
                    $contentData['audio_url'] = Storage::url($path);
                }
                unset($contentData['audio_file']);

                $exerciseDetail->update($contentData);
            }
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil diperbarui.');
    }

    /**
     * Menghapus exercise.
     */
    public function destroy(Exercise $exercise)
    {
        DB::transaction(function () use ($exercise) {
            if ($exercise->exerciseable) {
                // Jika ada file terkait (audio/gambar), hapus dari storage
                if (isset($exercise->exerciseable->audio_url)) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $exercise->exerciseable->audio_url));
                }
                if (isset($exercise->exerciseable->options)) {
                    foreach ($exercise->exerciseable->options as $option) {
                        if (Str::startsWith($option, '/storage')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $option));
                        }
                    }
                }
                $exercise->exerciseable->delete();
            }
            $exercise->delete();
        });

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil dihapus.');
    }

    private function sanitizeContent(string $type, array $content): array
    {
        switch ($type) {
            case 'matching_game':
                return ['pairs' => $content['pairs'] ?? []];
            case 'translation_match':
                return ['pairs' => array_values($content['pairs'] ?? [])];
            case 'speaking_practice':
                return ['prompt_text' => $content['prompt_text'] ?? ''];
            case 'silent_letter_hunt':
                return [
                    'sentence' => $content['sentence'] ?? '',
                    'words' => array_values($content['words'] ?? [])
                ];
            case 'spelling_quiz':
                return [
                    'audio_url' => $content['audio_url'] ?? '',
                    'correct_answer' => $content['correct_answer'] ?? '',
                ];
            case 'sound_sorting':
                return [
                    'categories' => array_values($content['categories'] ?? []),
                    'words' => array_values($content['words'] ?? [])
                ];
            case 'sentence_scramble':
                return ['sentence' => $content['sentence'] ?? ''];
            case 'fill_in_the_blank':
                return [
                    'sentence_parts' => $content['sentence_parts'] ?? [],
                    'correct_answer' => $content['correct_answer'] ?? ''
                ];
            case 'sequencing':
                return ['sentence' => $content['sentence'] ?? ''];
            case 'listening_task':
                return [
                    'instruction' => $content['instruction'] ?? '',
                    'options' => array_values($content['options'] ?? []),
                    'correct_answer' => $content['correct_answer'] ?? ''
                ];
            case 'fill_with_options':
                return [
                    'sentence_parts' => $content['sentence_parts'] ?? [],
                    'options' => array_values($content['options'] ?? []),
                    'correct_answer' => $content['correct_answer'] ?? ''
                ];
            case 'fill_multiple_blanks':
                return [
                    'sentence_parts' => $content['sentence_parts'] ?? [],
                    'correct_answers' => $content['correct_answers'] ?? []
                ];
            case 'multiple_choice_quiz':
                return [
                    'question_text' => $content['question_text'] ?? '',
                    'options' => $content['options'] ?? [],
                    'correct_answer' => $content['correct_answer'] ?? ''
                ];
            default:
                return [];
        }
    }
}
