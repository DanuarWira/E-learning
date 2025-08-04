<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'level',
        'order',
        'is_published',
    ];

    /**
     * Mendefinisikan relasi "satu-ke-banyak": Satu Module memiliki banyak Lesson.
     */
    public function lessons(): HasMany
    {
        // 'foreign_key', 'local_key'
        return $this->hasMany(Lesson::class, 'module_id', 'id');
    }

    public function getProgressForUser(User $user): int
    {
        $lessons = $this->lessons()->with(['vocabularies.items', 'exercises'])->get();
        if ($lessons->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;
        foreach ($lessons as $lesson) {
            $totalProgress += $lesson->getProgressFor($user);
        }

        return round($totalProgress / $lessons->count());
    }

    public function isCompleteFor(User $user): bool
    {
        return $this->getProgressForUser($user) >= 100;
    }
}
