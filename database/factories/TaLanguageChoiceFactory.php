<?php

namespace Database\Factories;

use App\Models\TaLanguageChoice;
use App\Models\TaPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaLanguageChoice>
 */
class TaLanguageChoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'preference_id' => TaPreference::factory(),
            'language_id' => fake()->numberBetween(1, 16) === 0 ? '001' : sprintf('%03d', fake()->numberBetween(1, 16)),
        ];
    }
}
