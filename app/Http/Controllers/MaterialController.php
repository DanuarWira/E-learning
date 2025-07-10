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
    /**
     * Menampilkan halaman utama manajemen material.
     */
    public function index(): View
    {
        $materials = Material::with(['lesson', 'items'])->orderBy('lesson_id')->paginate(10);
        $lessons = Lesson::orderBy('title')->get(); // Ambil lessons untuk dropdown di modal
        return view('superadmin.materials.index', compact('materials', 'lessons'));
    }

    /**
     * Menyimpan data dari form modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.file' => 'nullable|file|mimes:mp3,wav,jpeg,png,jpg,gif|max:2048',
            'items.*.url' => 'nullable|url',
            'items.*.title' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $material = Material::create($request->only('lesson_id', 'type'));
            foreach ($request->items as $index => $itemData) {
                $url = null;
                if ($request->hasFile("items.{$index}.file")) {
                    $path = $request->file("items.{$index}.file")->store('materials', 'public');
                    $url = Storage::url($path);
                } elseif (!empty($itemData['url'])) {
                    $url = $itemData['url'];
                }
                $itemData['url'] = $url;
                $material->items()->create($itemData);
            }
        });

        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dibuat.');
    }

    /**
     * Memperbarui data dari form modal.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.title' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $material) {
            $material->update($request->only('lesson_id', 'type'));

            $existingItemIds = collect($request->items)->pluck('id')->filter();
            $itemsToDelete = $material->items()->whereNotIn('id', $existingItemIds)->get();
            foreach ($itemsToDelete as $item) {
                if ($item->url && Str::startsWith($item->url, '/storage')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
                }
                $item->delete();
            }

            foreach ($request->items as $index => $itemData) {
                $url = $itemData['existing_url'] ?? null;
                if ($request->hasFile("items.{$index}.file")) {
                    if ($url && Str::startsWith($url, '/storage')) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $url));
                    }
                    $path = $request->file("items.{$index}.file")->store('materials', 'public');
                    $url = Storage::url($path);
                } elseif (!empty($itemData['url'])) {
                    $url = $itemData['url'];
                }
                $itemData['url'] = $url;

                if (isset($itemData['id'])) {
                    $material->items()->find($itemData['id'])->update($itemData);
                } else {
                    $material->items()->create($itemData);
                }
            }
        });

        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    /**
     * Menghapus material.
     */
    public function destroy(Material $material)
    {
        foreach ($material->items as $item) {
            if ($item->url && Str::startsWith($item->url, '/storage')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
            }
        }
        $material->delete();
        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dihapus.');
    }
}
