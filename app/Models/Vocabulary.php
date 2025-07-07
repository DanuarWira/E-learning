<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vocabulary extends Model
{
    use HasFactory;
    protected $fillable = ['lesson_id', 'category', 'order'];
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(VocabularyItem::class, 'vocabulary_id');
    }
}
