<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Panggil seeder spesifik untuk modul
        $this->call([
            VocabularySeeder::class,
            // ExerciseSeeder::class,
            InstansiSeeder::class,
            UserSeeder::class
        ]);

        // Anda bisa menambahkan seeder lain di sini jika ada
        // \App\Models\User::factory(10)->create();
    }
}
