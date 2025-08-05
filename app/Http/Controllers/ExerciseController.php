<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\ExerciseMatchingGame;
use App\Models\ExerciseMultipleChoice;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * Menampilkan satu halaman latihan interaktif.
     * Parameter $lesson diambil dari URL, meskipun kita bisa mendapatkannya dari $exercise->lesson.
     */
    public function show(Lesson $lesson, Exercise $exercise): View
    {
        // Memastikan latihan yang diakses benar-benar milik pelajaran yang ada di URL
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
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|array',
            'content.*' => 'nullable',
            'content.audio_file' => 'nullable|file|mimes:mp3,wav,ogg', // Validasi untuk file audio
        ]);

        DB::transaction(function () use ($validated, $request) {
            $modelClass = Relation::getMorphedModel($validated['type']);
            $contentData = $validated['content'];

            if ($modelClass && class_exists($modelClass)) {
                // --- LOGIKA UPLOAD FILE UNTUK SPELLING QUIZ ---
                if ($validated['type'] === 'spelling_quiz' && $request->hasFile('content.audio_file')) {
                    // Simpan file ke public/storage/audio dan dapatkan path-nya
                    $path = $request->file('content.audio_file')->store('audio', 'public');
                    // Simpan URL yang bisa diakses publik
                    $contentData['audio_url'] = Storage::url($path);
                }
                unset($contentData['audio_file']); // Hapus file dari data sebelum create
                // ---------------------------------------------

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
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
            'content.audio_file' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        DB::transaction(function () use ($validated, $request, $exercise) {
            $exercise->update([
                'lesson_id' => $validated['lesson_id'],
                'title' => $validated['title'],
            ]);

            $contentData = $validated['content'];
            $exerciseDetail = $exercise->exerciseable;

            if ($exerciseDetail) {
                // --- LOGIKA UPLOAD FILE UNTUK SPELLING QUIZ (UPDATE) ---
                if ($exercise->exerciseable_type === 'spelling_quiz' && $request->hasFile('content.audio_file')) {
                    // Hapus file audio lama jika ada
                    if ($exerciseDetail->audio_url) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $exerciseDetail->audio_url));
                    }
                    // Simpan file baru
                    $path = $request->file('content.audio_file')->store('audio', 'public');
                    $contentData['audio_url'] = Storage::url($path);
                }
                unset($contentData['audio_file']);
                // ----------------------------------------------------

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
