<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercise_fill_with_options', function (Blueprint $table) {
            $table->id();
            $table->json('sentence_parts'); // ["Part 1", "Part 2", ...]
            $table->json('options');        // ["Option 1", "Option 2", ...]
            $table->string('correct_answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_fill_with_options');
    }
};
