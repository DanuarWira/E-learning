<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Instansi;
use Illuminate\Support\Facades\DB;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan foreign key check sebelum truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Instansi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Instansi::create(['name' => 'Instansi A']);
        Instansi::create(['name' => 'Instansi B']);
        Instansi::create(['name' => 'Instansi C']);
    }
}
