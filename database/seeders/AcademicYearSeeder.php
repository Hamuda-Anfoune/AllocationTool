<?php

namespace Database\Seeders;

use \App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $academicYears = [
        [
          'id' => Carbon::now()->year . "-" . Carbon::now()->subYear()->year,
          'is_active' => true
        ],
        [
          'id' => Carbon::now()->addYear()->year . "-" . Carbon::now()->year,
          'is_active' => true
        ]
      ];

    foreach ($academicYears as $academicYear) {
      AcademicYear::create($academicYear);
    }
  }
}
