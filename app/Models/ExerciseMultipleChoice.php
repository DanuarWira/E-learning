<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExerciseMultipleChoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_text',
        'question_media_url',
        'question_media_type',
        'options',
        'correct_answer'
    ];
    protected $casts = ['options' => 'array'];

    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
