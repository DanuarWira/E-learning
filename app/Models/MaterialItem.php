<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Completable;

class MaterialItem extends Model
{
    use Completable;
    use HasFactory;
    protected $fillable = ['material_id', 'title', 'description', 'media_url', 'order'];
    protected $casts = [
        'description' => 'array',
    ];
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
