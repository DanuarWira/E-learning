<?php

namespace App\Models\Concerns;

use App\Models\Completion;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Completable
{
    public function completions(): MorphMany
    {
        return $this->morphMany(Completion::class, 'completable');
    }
}
