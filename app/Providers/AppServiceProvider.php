<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan 'morph map' di sini
        Relation::morphMap([
            'multiple_choice_quiz' => \App\Models\ExerciseMultipleChoice::class,
            'matching_game' => \App\Models\ExerciseMatchingGame::class,
            'pronunciation_drill' => \App\Models\ExercisePronunciationDrill::class,
            'translation_match' => \App\Models\ExerciseTranslationMatch::class,
            'silent_letter_hunt' => \App\Models\ExerciseSilentLetterHunt::class,
            'spelling_quiz' => \App\Models\ExerciseSpellingQuiz::class,
            'sound_sorting' => \App\Models\ExerciseSoundSorting::class,
            'sentence_scramble' => \App\Models\ExerciseSentenceScramble::class,
        ]);
    }
}
