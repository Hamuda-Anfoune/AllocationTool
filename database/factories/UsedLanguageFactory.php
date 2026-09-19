<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\UsedLanguage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsedLanguage>
 */
class UsedLanguageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'language_id' => sprintf('%03d', fake()->numberBetween(1, 16)),
            'priority' => 1,
            'academic_year' => '2020-2021-01',
        ];
    }
}
