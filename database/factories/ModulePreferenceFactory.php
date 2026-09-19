<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\ModulePreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModulePreference>
 */
class ModulePreferenceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'no_of_assistants' => 1,
            'no_of_contact_hours' => fake()->numberBetween(2, 8),
            'no_of_marking_hours' => fake()->numberBetween(2, 8),
            'academic_year' => '2020-2021-01',
        ];
    }
}
