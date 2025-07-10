<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Vocabulary;
use App\Models\VocabularyItem;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VocabularyController extends Controller
{
    public function index(): View
    {
        $vocabularies = Vocabulary::with(['lesson', 'items'])->orderBy('lesson_id')->paginate(10);
        $lessons = Lesson::orderBy('title')->get(); // Ambil lessons untuk dropdown di modal
        return view('superadmin.vocabularies.index', compact('vocabularies', 'lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'category' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.term' => 'required|string|max:255',
            'items.*.details' => 'nullable|string',
            'items.*.media' => 'nullable|file|mimes:mp3,wav,mp4|max:5120', // Validasi file
        ]);

        DB::transaction(function () use ($request) {
            $vocabulary = Vocabulary::create($request->only('lesson_id', 'category'));

            foreach ($request->items as $index => $itemData) {
                if ($request->hasFile("items.{$index}.media")) {
                    $path = $request->file("items.{$index}.media")->store('vocab_media', 'public');
                    $itemData['media_url'] = Storage::url($path);
                }
                $vocabulary->items()->create($itemData);
            }
        });

        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil dibuat.');
    }

    public function update(Request $request, Vocabulary $vocabulary)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'category' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.term' => 'required|string|max:255',
            'items.*.details' => 'nullable|string',
            'items.*.media' => 'nullable|file|mimes:mp3,wav,mp4|max:5120',
        ]);

        DB::transaction(function () use ($request, $vocabulary) {
            $vocabulary->update($request->only('lesson_id', 'category'));

            $existingItemIds = collect($request->items)->pluck('id')->filter();
            $itemsToDelete = $vocabulary->items()->whereNotIn('id', $existingItemIds)->get();
            foreach ($itemsToDelete as $item) {
                if ($item->media_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $item->media_url));
                }
                $item->delete();
            }

            foreach ($request->items as $index => $itemData) {
                $url = $itemData['existing_media_url'] ?? null;
                if ($request->hasFile("items.{$index}.media")) {
                    if ($url) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $url));
                    }
                    $path = $request->file("items.{$index}.media")->store('vocab_media', 'public');
                    $url = Storage::url($path);
                }
                $itemData['media_url'] = $url;

                if (isset($itemData['id'])) {
                    $vocabulary->items()->find($itemData['id'])->update($itemData);
                } else {
                    $vocabulary->items()->create($itemData);
                }
            }
        });

        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil diperbarui.');
    }

    public function destroy(Vocabulary $vocabulary)
    {
        foreach ($vocabulary->items as $item) {
            if ($item->media_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->media_url));
            }
        }
        $vocabulary->delete();
        return redirect()->route('superadmin.vocabularies.index')->with('success', 'Vocabulary berhasil dihapus.');
    }
}
