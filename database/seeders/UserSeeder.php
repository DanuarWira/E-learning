<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // PERBAIKAN: Matikan foreign key check sebelum truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data user lama
        User::truncate();

        // PERBAIKAN: Nyalakan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::create([
            'name' => 'Super Admin',
            'instansi' => 'EngageEnglish HQ',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Supervisor Hotel A',
            'instansi' => 'Hotel A',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        User::create([
            'name' => 'User Biasa',
            'instansi' => 'Hotel B',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user', // atau biarkan kosong untuk menggunakan nilai default
        ]);
    }
}
