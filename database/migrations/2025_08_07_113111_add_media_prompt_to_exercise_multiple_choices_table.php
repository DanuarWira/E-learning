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
        Schema::table('exercise_multiple_choices', function (Blueprint $table) {
            $table->string('question_media_url')->nullable()->after('question_text');
            $table->string('question_media_type')->nullable()->after('question_media_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercise_multiple_choices', function (Blueprint $table) {
            $table->dropColumn(['question_media_url', 'question_media_type']);
        });
    }
};
