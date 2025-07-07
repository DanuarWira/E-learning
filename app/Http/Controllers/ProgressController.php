<?php
// FILE: app/Http/Controllers/ProgressController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $modelClass = "App\\Models\\{$validated['type']}"; // Cth: App\Models\VocabularyItem

        if (!class_exists($modelClass)) {
            return response()->json(['message' => 'Tipe tidak valid.'], 400);
        }

        foreach ($validated['items'] as $itemId) {
            $user->completions()->updateOrCreate([
                'completable_id' => $itemId,
                'completable_type' => $modelClass,
            ]);
        }

        return response()->json(['message' => 'Progress berhasil disimpan.']);
    }
}
