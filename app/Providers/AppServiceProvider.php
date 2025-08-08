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
        Relation::morphMap([
            'multiple_choice_quiz' => \App\Models\ExerciseMultipleChoice::class,
            'matching_game' => \App\Models\ExerciseMatchingGame::class,
            'translation_match' => \App\Models\ExerciseTranslationMatch::class,
            'silent_letter_hunt' => \App\Models\ExerciseSilentLetterHunt::class,
            'spelling_quiz' => \App\Models\ExerciseSpellingQuiz::class,
            'sound_sorting' => \App\Models\ExerciseSoundSorting::class,
            'sentence_scramble' => \App\Models\ExerciseSentenceScramble::class,
            'fill_multiple_blanks' => \App\Models\ExerciseFillMultipleBlank::class,
            'sequencing' => \App\Models\ExerciseSequencing::class,
            'fill_with_options' => \App\Models\ExerciseFillWithOptions::class,
            'speaking_quiz' => \App\Models\ExerciseSpeakingQuiz::class,
        ]);
    }
}
