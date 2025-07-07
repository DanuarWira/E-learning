<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;
    protected $fillable = ['lesson_id', 'type', 'order'];
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function items()
    {
        return $this->hasMany(MaterialItem::class);
    }
}
