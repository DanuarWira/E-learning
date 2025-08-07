<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Instansi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Matikan foreign key check sebelum truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil ID dari Instansi A untuk digunakan pada user
        $instansiA = Instansi::where('name', 'Instansi A')->first();

        if ($instansiA) {
            User::create([
                'name' => 'Super Admin',
                'instansi_id' => $instansiA->id,
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]);

            User::create([
                'name' => 'Supervisor Hotel A',
                'instansi_id' => $instansiA->id,
                'email' => 'supervisor@example.com',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
            ]);

            User::create([
                'name' => 'User Biasa',
                'instansi_id' => $instansiA->id,
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }
    }
}
