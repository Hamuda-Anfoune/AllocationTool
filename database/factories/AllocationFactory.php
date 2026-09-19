<?php

namespace Database\Factories;

use App\Models\Allocation;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Allocation>
 */
class AllocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $academicYear = '2020-2021-01';

        return [
            'allocation_id' => $academicYear.'-A-01',
            'academic_year' => $academicYear,
            'module_id' => Module::factory(),
            'ta_id' => User::factory()->graduateTa(),
            'creator_email' => User::factory()->admin(),
        ];
    }
}
