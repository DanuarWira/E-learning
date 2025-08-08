<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\VocabularyItem;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['module_id', 'title', 'slug', 'order'];
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
    public function vocabularies(): HasMany
    {
        return $this->hasMany(Vocabulary::class);
    }
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function getProgressFor(User $user)
    {
        $totalItems = 0;
        $completedItems = 0;

        $vocabCategories = $this->vocabularies()->with('items')->get();
        foreach ($vocabCategories as $category) {
            $totalItems += $category->items->count();
            $completedItems += $user->completions()
                ->where('completable_type', VocabularyItem::class)
                ->whereIn('completable_id', $category->items->pluck('id'))
                ->count();
        }

        if ($totalItems === 0) {
            return 0;
        }

        return round(($completedItems / $totalItems) * 100);
    }

    public function isCompleteFor(User $user): bool
    {
        return $this->getProgressFor($user) >= 100;
    }
}
