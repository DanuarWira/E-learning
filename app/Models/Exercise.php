<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Completable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Exercise extends Model
{
    use Completable;
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'order',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function exerciseable(): MorphTo
    {
        return $this->morphTo();
    }
}
