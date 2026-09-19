<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\ModuleRankOrderList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleRankOrderList>
 */
class ModuleRankOrderListFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year' => '2020-2021-01',
            'module_id' => Module::factory(),
            'ta_email' => User::factory()->graduateTa(),
            'ta_total_weight' => fake()->numberBetween(0, 100),
            'did_before_weight' => 0,
            'module_priority_for_ta' => 1,
            'module_priority_for_ta_weight' => 0,
            'languages_similarity_weight' => 0,
        ];
    }
}
