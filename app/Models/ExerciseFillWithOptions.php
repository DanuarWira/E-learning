<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExerciseFillWithOptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'sentence_parts',
        'options',
        'correct_answer',
    ];

    protected $casts = [
        'sentence_parts' => 'array',
        'options' => 'array',
    ];

    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
