<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => strtoupper(fake()->unique()->bothify('CS###')),
            'module_name' => fake()->sentence(3),
            'convenor_email' => User::factory()->convenor(),
            'academic_year' => '2020-2021-01',
        ];
    }
}
