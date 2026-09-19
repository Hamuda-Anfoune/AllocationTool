<?php

namespace Database\Factories;

use App\Models\TaAllocationData;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaAllocationData>
 */
class TaAllocationDataFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $academicYear = '2020-2021-01';

        return [
            'allocation_id' => $academicYear.'-A-01',
            'ta_id' => User::factory()->graduateTa(),
            'contact_hours' => fake()->numberBetween(2, 10),
            'marking_hours' => fake()->numberBetween(2, 10),
            'academic_year' => $academicYear,
        ];
    }
}
