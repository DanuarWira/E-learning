<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::with(['lesson', 'items'])->orderBy('lesson_id')->paginate(10);
        $lessons = Lesson::orderBy('title')->get();
        return view('superadmin.materials.index', compact('materials', 'lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|array',
            'items.*.description.*.chunk' => 'required_with:items.*.description|string',
            'items.*.description.*.translation' => 'required_with:items.*.description|string',
            'items.*.media' => ['nullable', 'file', 'mimes:mp3,wav,mp4,jpeg,png,jpg,gif', 'max:5120'],
        ]);

        DB::transaction(function () use ($request) {
            $material = Material::create($request->only('lesson_id', 'type'));

            foreach ($request->items as $index => $itemData) {
                if ($request->hasFile("items.{$index}.media")) {
                    $path = $request->file("items.{$index}.media")->store('material_media', 'public');
                    $itemData['media_url'] = Storage::url($path);
                }
                $material->items()->create($itemData);
            }
        });

        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dibuat.');
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|array',
            'items.*.description.*.chunk' => 'required_with:items.*.description|string',
            'items.*.description.*.translation' => 'required_with:items.*.description|string',
            'items.*.media' => ['nullable', 'file', 'mimes:mp3,wav,mp4,jpeg,png,jpg,gif', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $material) {
            $material->update($request->only('lesson_id', 'type'));

            $existingItemIds = collect($request->items)->pluck('id')->filter();
            $itemsToDelete = $material->items()->whereNotIn('id', $existingItemIds)->get();
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
                    $path = $request->file("items.{$index}.media")->store('material_media', 'public');
                    $url = Storage::url($path);
                }
                $itemData['media_url'] = $url;

                if (isset($itemData['id'])) {
                    $material->items()->find($itemData['id'])->update($itemData);
                } else {
                    $material->items()->create($itemData);
                }
            }
        });

        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        foreach ($material->items as $item) {
            if ($item->media_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->media_url));
            }
        }
        $material->delete();
        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dihapus.');
    }
}
