<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_sound_sortings', function (Blueprint $table) {
            $table->id();
            $table->json('categories'); // [{name: '/r/ sound', id: 'r_sound'}, ...]
            $table->json('words');      // [{word: 'room', category_id: 'r_sound'}, ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_sound_sortings');
    }
};
