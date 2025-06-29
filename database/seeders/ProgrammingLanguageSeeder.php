<?php

namespace Database\Seeders;

use App\Models\ProgrammingLanguage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgrammingLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programmingLanguages = [
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Python'],
            ['name' => 'Java'],
            ['name' => 'C'],
            ['name' => 'C++'],
            ['name' => 'C#'],
            ['name' => 'Ruby'],
            ['name' => 'Go'],
            ['name' => 'Rust'],
            ['name' => 'Swift'],
            ['name' => 'Kotlin'],
            ['name' => 'Kotlin/Native'],
            ['name' => 'TypeScript'],
            ['name' => 'SQL'],
            ['name' => 'HTML/CSS'],
            ['name' => 'Bash/Shell'],
            ['name' => 'Elm'],
            ['name' => 'Rust'],
            ['name' => 'VB.NET'],
            ['name' => 'Dart'],
        ];

        foreach ($programmingLanguages as $programmingLanguage) {
            ProgrammingLanguage::create($programmingLanguage);
        }
    }
}
