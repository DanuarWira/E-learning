<?php

namespace App\Http\Controllers;

use App\Models\Completion;
use App\Models\Module;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\MaterialItem;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    public function show(): View
    {
        return view('onboarding');
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        if ($request->input('skip') == '1') {
            $preLearningModule = Module::where('slug', 'pre-learning')->first();

            if ($preLearningModule) {
                $completionsToInsert = [];
                $now = Carbon::now();
                $lessonIds = $preLearningModule->lessons->pluck('id');

                $vocabItemIds = VocabularyItem::whereHas('vocabulary', function ($query) use ($lessonIds) {
                    $query->whereIn('lesson_id', $lessonIds);
                })->pluck('id');

                foreach ($vocabItemIds as $id) {
                    $completionsToInsert[] = [
                        'user_id' => $user->id,
                        'completable_id' => $id,
                        'completable_type' => VocabularyItem::class,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($completionsToInsert)) {
                    Completion::insert($completionsToInsert);
                }
            }
        }

        $user->has_completed_onboarding = true;
        $user->save();

        return redirect()->route('dashboard');
    }
}
