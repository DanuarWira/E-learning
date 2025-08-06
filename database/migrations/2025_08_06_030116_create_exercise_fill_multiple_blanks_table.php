<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_fill_multiple_blanks', function (Blueprint $table) {
            $table->id();
            $table->json('sentence_parts');
            $table->json('correct_answers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_fill_multiple_blanks');
    }
};
