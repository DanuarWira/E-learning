<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Menampilkan detail sebuah modul beserta pelajarannya.
     *
     * @param Module $module
     * @return View
     */
    public function show(Module $module): View
    {
        $user = Auth::user();
        // Ambil semua lesson, diurutkan berdasarkan urutan yang benar
        $lessons = $module->lessons()->orderBy('order', 'asc')->get();

        $previousLessonIsComplete = true; // Lesson pertama selalu tidak terkunci

        foreach ($lessons as $lesson) {
            $lesson->is_locked = !$previousLessonIsComplete;
            if (!$lesson->is_locked) {
                $previousLessonIsComplete = $lesson->isCompleteFor($user);
            }
        }

        // Ganti relasi 'lessons' pada modul dengan koleksi yang sudah kita proses
        $module->setRelation('lessons', $lessons);

        return view('module', compact('module'));
    }

    public function index(): View
    {
        $modules = Module::withCount('lessons')->latest()->paginate(10);
        return view('superadmin.modules.index', compact('modules'));
    }

    public function create(): View
    {
        return view('superadmin.modules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:modules,title',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        Module::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'level' => $request->level,
            'is_published' => $request->has('is_published') ? 1 : 0,
        ]);

        return redirect()->route('superadmin.modules.index')->with('success', 'Modul berhasil dibuat.');
    }

    public function edit(Module $module): View
    {
        return view('superadmin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:modules,title,' . $module->id,
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        $module->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'level' => $request->level,
            'is_published' => $request->has('is_published') ? 1 : 0,
        ]);

        return redirect()->route('superadmin.modules.index')->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('superadmin.modules.index')->with('success', 'Modul berhasil dihapus.');
    }
}
