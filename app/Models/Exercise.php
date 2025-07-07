<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Completable;

class Exercise extends Model
{
    use Completable;
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'content',
        'order',
    ];

    /**
     * Ini adalah bagian yang paling penting.
     * Baris ini memberitahu Laravel untuk otomatis mengubah
     * kolom 'content' dari array menjadi JSON saat menyimpan,
     * dan sebaliknya saat mengambil.
     */
    protected $casts = [
        'content' => 'array',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
