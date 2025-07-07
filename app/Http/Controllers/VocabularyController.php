<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Vocabulary;
use App\Models\VocabularyItem;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VocabularyController extends Controller
{
    public function index(): View
    {
        $vocabularies = Vocabulary::with(['lesson', 'items'])->orderBy('lesson_id')->paginate(10);
        $lessons = Lesson::orderBy('title')->get(); // Ambil lessons untuk dropdown di modal
        return view('superadmin.vocabularies.index', compact('vocabularies', 'lessons'));
    }

    // public function create(): View
    // {
    //     $lessons = Lesson::all();
    //     return view('superadmin.vocabularies.create', compact('lessons'));
    // }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'category' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.term' => 'required|string|max:255',
            'items.*.details' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $vocabulary = Vocabulary::create($request->only('lesson_id', 'category'));
            $vocabulary->items()->createMany($request->items);
        });

        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil dibuat.');
    }

    // public function edit(Vocabulary $vocabulary): View
    // {
    //     $lessons = Lesson::all();
    //     $vocabulary->load('items'); // Pastikan items sudah dimuat
    //     return view('superadmin.vocabularies.edit', compact('vocabulary', 'lessons'));
    // }

    public function update(Request $request, Vocabulary $vocabulary)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'category' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.term' => 'required|string|max:255',
            'items.*.details' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $vocabulary) {
            $vocabulary->update($request->only('lesson_id', 'category'));
            $vocabulary->items()->delete(); // Hapus item lama
            $vocabulary->items()->createMany($request->items); // Buat ulang dari form
        });

        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil diperbarui.');
    }

    public function destroy(Vocabulary $vocabulary)
    {
        $vocabulary->delete();
        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil dihapus.');
    }
}
