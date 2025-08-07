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
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom 'instansi' yang lama jika ada
            if (Schema::hasColumn('users', 'instansi')) {
                $table->dropColumn('instansi');
            }

            // PERBAIKAN: Pastikan 'constrained' merujuk ke tabel 'instansis'
            $table->foreignId('instansi_id')->nullable()->constrained('instansis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['instansi_id']);
            $table->dropColumn('instansi_id');
            $table->string('instansi')->nullable(); // Kembalikan kolom lama jika di-rollback
        });
    }
};
