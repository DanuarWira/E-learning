<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'completable_id', 'completable_type'];
}
