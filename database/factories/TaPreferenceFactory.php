<?php

namespace Database\Factories;

use App\Models\TaPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaPreference>
 */
class TaPreferenceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taEmail = User::factory()->graduateTa();
        $academicYear = '2020-2021-01';

        return [
            'preference_id' => fake()->unique()->bothify('ta_????????Y2020-2021-01'),
            'ta_email' => $taEmail,
            'max_contact_hours' => 10,
            'max_marking_hours' => 10,
            'max_modules' => 2,
            'academic_year' => $academicYear,
            'have_tier4_visa' => false,
        ];
    }

    /**
     * Attach this preference to a specific, already-created TA.
     */
    public function forTa(User $ta): static
    {
        return $this->state([
            'ta_email' => $ta->email,
            'preference_id' => fake()->unique()->bothify('??????????'),
        ]);
    }
}
