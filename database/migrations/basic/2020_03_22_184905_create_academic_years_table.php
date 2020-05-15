<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAcademicYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            // $table->bigIncrements('field_id');
            $table->string('year', 15)->primary();
            $table->boolean('current');
            $table->timestamps();
        });

        DB::table('academic_years')->insert(
            array(
                [
                    'year' => '2020-2021-01',
                    'current' => 0
                ],
                [
                    'year' => '2020-2021-02',
                    'current' => 0
                ],
                [
                    'year' => '2021-2022-01',
                    'current' => 0
                ],
                [
                    'year' => '2021-2022-02',
                    'current' => 0
                ],
                [
                    'year' => '2022-2023-01',
                    'current' => 0
                ],
                [
                    'year' => '2022-2023-02',
                    'current' => 0
                ],
                [
                    'year' => '2023-2024-01',
                    'current' => 0
                ],
                [
                    'year' => '2023-2024-02',
                    'current' => 0
                ],
                [
                    'year' => '2024-2025-01',
                    'current' => 0
                ],
                [
                    'year' => '2024-2025-02',
                    'current' => 0
                ],
                [
                    'year' => '2025-2026-01',
                    'current' => 0
                ],
                [
                    'year' => '2025-2026-02',
                    'current' => 0
                ],
                [
                    'year' => '2026-2027-01',
                    'current' => 0
                ],
                [
                    'year' => '2026-2027-02',
                    'current' => 0
                ],
                [
                    'year' => '2027-2028-01',
                    'current' => 0
                ],
                [
                    'year' => '2027-2028-02',
                    'current' => 0
                ],
                [
                    'year' => '2028-2029-01',
                    'current' => 0
                ],
                [
                    'year' => '2028-2029-02',
                    'current' => 0
                ],
                [
                    'year' => '2029-2030-01',
                    'current' => 0
                ],
                [
                    'year' => '2029-2030-02',
                    'current' => 0
                ]
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_years');
    }
}
