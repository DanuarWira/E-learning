<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExercisePronunciationDrill extends Model
{
    use HasFactory;

    protected $fillable = ['prompt_text'];

    /**
     * Mendapatkan relasi ke model Exercise utama.
     */
    public function exercise(): MorphOne
    {
        return $this->morphOne(Exercise::class, 'exerciseable');
    }
}
