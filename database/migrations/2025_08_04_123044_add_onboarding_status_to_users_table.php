<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini akan melacak apakah pengguna sudah melewati halaman onboarding.
            $table->boolean('has_completed_onboarding')->default(false)->after('role');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_completed_onboarding');
        });
    }
};
