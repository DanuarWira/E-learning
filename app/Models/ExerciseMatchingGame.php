<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExerciseMatchingGame extends Model
{
    use HasFactory;
    protected $fillable = ['instruction', 'pairs'];
    protected $casts = ['pairs' => 'array'];

    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
