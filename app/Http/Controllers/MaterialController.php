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
        $lessons = Lesson::orderBy('title')->get();
        return view('superadmin.materials.index', compact('materials', 'lessons'));
    }

    /**
     * Menyimpan data dari form modal.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp3,wav|max:2048',
            'items.*.audio_file' => 'nullable|file|mimes:mp3,wav|max:2048',
            'items.*.url' => 'nullable|url',
        ]);

        DB::transaction(function () use ($request, $validatedData) {
            $material = Material::create($request->only('lesson_id', 'type'));

            foreach ($validatedData['items'] as $index => $itemData) {
                $dataToCreate = [
                    'title' => $itemData['title'] ?? null,
                    'description' => $itemData['description'],
                    'url' => $itemData['url'] ?? null,
                ];

                if ($request->hasFile("items.{$index}.file")) {
                    $path = $request->file("items.{$index}.file")->store('material_media', 'public');
                    $dataToCreate['url'] = Storage::url($path);
                }
                if ($request->hasFile("items.{$index}.audio_file")) {
                    $audioPath = $request->file("items.{$index}.audio_file")->store('material_media/audio', 'public');
                    $dataToCreate['audio_url'] = Storage::url($audioPath);
                }

                $material->items()->create($dataToCreate);
            }
        });

        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dibuat.');
    }

    public function update(Request $request, Material $material)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer|exists:material_items,id',
            'items.*.description' => 'required|string',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp3,wav|max:2048',
            'items.*.audio_file' => 'nullable|file|mimes:mp3,wav|max:2048',
            'items.*.url' => 'nullable|url',
        ]);

        DB::transaction(function () use ($request, $validatedData, $material) {
            $material->update($request->only('lesson_id', 'type'));

            $incomingItemIds = collect($validatedData['items'])->pluck('id')->filter();
            $itemsToDelete = $material->items()->whereNotIn('id', $incomingItemIds)->get();
            foreach ($itemsToDelete as $item) {
                if ($item->url && Str::startsWith($item->url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
                if ($item->audio_url && Str::startsWith($item->audio_url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->audio_url));
                $item->delete();
            }

            foreach ($validatedData['items'] as $index => $itemData) {
                $item = isset($itemData['id']) ? $material->items()->find($itemData['id']) : null;

                $dataToUpdate = [
                    'title' => $itemData['title'] ?? null,
                    'description' => $itemData['description'],
                    'url' => $item->url ?? $itemData['url'] ?? null,
                    'audio_url' => $item->audio_url ?? null,
                ];

                if ($request->hasFile("items.{$index}.file")) {
                    if ($item && $item->url && Str::startsWith($item->url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
                    $path = $request->file("items.{$index}.file")->store('materials', 'public');
                    $dataToUpdate['url'] = Storage::url($path);
                }

                if ($request->hasFile("items.{$index}.audio_file")) {
                    if ($item && $item->audio_url && Str::startsWith($item->audio_url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->audio_url));
                    $audioPath = $request->file("items.{$index}.audio_file")->store('materials/audio', 'public');
                    $dataToUpdate['audio_url'] = Storage::url($audioPath);
                }

                if ($item) {
                    $item->update($dataToUpdate);
                } else {
                    $material->items()->create($dataToUpdate);
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
            if ($item->url && Str::startsWith($item->url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
            if ($item->audio_url && Str::startsWith($item->audio_url, '/storage')) Storage::disk('public')->delete(str_replace('/storage/', '', $item->audio_url));
        }
        $material->delete();
        return redirect()->route('superadmin.materials.index')->with('success', 'Material berhasil dihapus.');
    }
}
