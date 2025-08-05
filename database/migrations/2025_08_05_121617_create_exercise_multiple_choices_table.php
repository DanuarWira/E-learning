<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercise_multiple_choices', function (Blueprint $table) {
            $table->id();
            $table->text('question_text')->nullable();
            // Pilihan jawaban disimpan sebagai JSON di sini untuk kesederhanaan
            $table->json('options');
            $table->string('correct_answer');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('exercise_multiple_choices');
    }
};
