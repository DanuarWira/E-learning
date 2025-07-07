<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Module;
use App\Models\Lesson;
// Model Vocabulary, Material, dan Exercise dihapus dari sini

class ModuleSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Module::truncate();
        Lesson::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // MODUL 1
        $module1 = Module::create(['title' => 'Percakapan Dasar untuk Pemula', 'slug' => Str::slug('Percakapan Dasar untuk Pemula'), 'description' => 'Pelajari dasar-dasar percakapan bahasa Inggris.', 'level' => 'beginner', 'order' => 1, 'is_published' => true]);
        Lesson::create(['module_id' => $module1->id, 'title' => "English Alphabet and Pronunciation Basics", 'slug' => Str::slug("English Alphabet and Pronunciation Basics"), 'order' => 1]);
        Lesson::create(['module_id' => $module1->id, 'title' => "Essential Greetings and Self-Introductions", 'slug' => Str::slug("Essential Greetings and Self-Introductions"), 'order' => 2]);

        // MODUL 2
        $module2 = Module::create(['title' => 'Bahasa Inggris untuk Perhotelan', 'slug' => Str::slug('Bahasa Inggris untuk Perhotelan'), 'description' => 'Kosakata penting untuk industri perhotelan.', 'level' => 'beginner', 'order' => 2, 'is_published' => true]);
        Lesson::create(['module_id' => $module2->id, 'title' => "Menangani Reservasi di Telepon", 'slug' => Str::slug("Menangani Reservasi di Telepon"), 'order' => 1]);

        // MODUL 3
        $module3 = Module::create(['title' => 'Grammar Tingkat Menengah', 'slug' => Str::slug('Grammar Tingkat Menengah'), 'description' => 'Pahami tenses dan struktur kalimat kompleks.', 'level' => 'intermediate', 'order' => 3, 'is_published' => true]);
        Lesson::create(['module_id' => $module3->id, 'title' => "Advanced Pronunciation Practice", 'slug' => Str::slug("Advanced Pronunciation Practice"), 'order' => 1]);

        // MODUL 4
        $module4 = Module::create(['title' => 'Persiapan Wawancara Kerja', 'slug' => Str::slug('Persiapan Wawancara Kerja'), 'description' => 'Tingkatkan kepercayaan diri Anda.', 'level' => 'intermediate', 'order' => 4, 'is_published' => false]);
        Lesson::create(['module_id' => $module4->id, 'title' => "Menjawab Pertanyaan Umum Interview", 'slug' => Str::slug("Menjawab Pertanyaan Umum Interview"), 'order' => 1]);
    }
}
