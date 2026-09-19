<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaModuleChoice>
 */
class TaModuleChoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $preference = TaPreference::factory();

        return [
            'preference_id' => $preference,
            'ta_email' => fn (array $attributes) => TaPreference::find($attributes['preference_id'])?->ta_email
                ?? TaPreference::factory()->create()->ta_email,
            'module_id' => Module::factory(),
            'priority' => 1,
            'did_before' => false,
        ];
    }
}
