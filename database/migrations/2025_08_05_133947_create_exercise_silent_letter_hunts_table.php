<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_silent_letter_hunts', function (Blueprint $table) {
            $table->id();
            $table->text('sentence');
            $table->json('words');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_silent_letter_hunts');
    }
};
