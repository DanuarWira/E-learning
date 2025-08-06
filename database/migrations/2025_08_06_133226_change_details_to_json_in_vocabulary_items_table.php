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
        Schema::table('vocabulary_items', function (Blueprint $table) {
            // Mengubah tipe kolom 'details' dari string menjadi JSON
            $table->json('details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vocabulary_items', function (Blueprint $table) {
            // Mengembalikan tipe kolom jika migration di-rollback
            $table->string('details')->nullable()->change();
        });
    }
};
