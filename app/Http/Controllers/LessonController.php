<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Material;
use App\Models\Module;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson): View
    {
        $lesson->load(['vocabularies']);

        return view('lesson', compact('lesson'));
    }

    public function practice(Lesson $lesson, Vocabulary $vocabulary): View
    {
        $vocabulary->load('items');

        return view('practice', compact('lesson', 'vocabulary'));
    }

    public function material(Lesson $lesson, Material $material): View
    {
        $material->load('items');

        return view('material', compact('lesson', 'material'));
    }

    public function practiceExercises(Lesson $lesson): View
    {
        $exercises = $lesson->exercises()
            ->with('exerciseable')
            ->orderBy('order')
            ->get();

        return view('exercise', [
            'lesson' => $lesson,
            'exercises' => $exercises,
        ]);
    }

    public function index(): View
    {
        $lessons = Lesson::with('module')->latest()->paginate(10);
        $modules = Module::orderBy('title')->get();
        return view('superadmin.lessons.index', compact('lessons', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'slug' => 'required|string|unique:lessons,slug',
        ]);

        Lesson::create([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'module_id' => $request->module_id,
        ]);

        return redirect()->route('superadmin.lessons.index')->with('success', 'Lesson berhasil dibuat.');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'slug' => 'required|string|unique:lessons,slug,' . $lesson->id,
        ]);

        $lesson->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'module_id' => $request->module_id,
        ]);

        return redirect()->route('superadmin.lessons.index')->with('success', 'Lesson berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('superadmin.lessons.index')->with('success', 'Lesson berhasil dihapus.');
    }

    public function markAsComplete(Request $request, Lesson $lesson)
    {
        $lesson->progress()->updateOrCreate(
            ['user_id' => Auth::id()],
            []
        );
        return redirect()->route('modules.show', $lesson->module)->with('success', 'Pelajaran Selesai!');
    }
}
