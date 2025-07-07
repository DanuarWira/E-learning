<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
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
        $exercises = Exercise::with('lesson')->latest()->paginate(10);
        $lessons = Lesson::orderBy('title')->get();
        return view('superadmin.exercises.index', compact('exercises', 'lessons'));
    }

    /**
     * Menyimpan exercise baru dari form modal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|array',
        ]);

        Exercise::create($validated);

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
            'type' => 'required|string',
            'content' => 'required|array',
        ]);

        $exercise->update($validated);

        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil diperbarui.');
    }

    /**
     * Menghapus exercise.
     */
    public function destroy(Exercise $exercise)
    {
        $exercise->delete();
        return redirect()->route('superadmin.exercises.index')->with('success', 'Latihan berhasil dihapus.');
    }
}
