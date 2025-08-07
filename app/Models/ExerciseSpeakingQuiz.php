<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExerciseSpeakingQuiz extends Model
{
    use HasFactory;

    protected $fillable = ['media_url', 'media_type', 'hints', 'prompt_text'];

    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
