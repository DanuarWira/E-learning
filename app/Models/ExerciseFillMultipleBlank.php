<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExerciseFillMultipleBlank extends Model
{
    use HasFactory;

    protected $fillable = ['sentence_parts', 'correct_answers'];

    protected $casts = [
        'sentence_parts' => 'array',
        'correct_answers' => 'array',
    ];

    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
