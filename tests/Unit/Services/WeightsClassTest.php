<?php

namespace Tests\Unit\Services;

use App\Models\Allocation;
use App\Models\Module;
use App\Models\User;
use App\Services\WeightsClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests pinning down the behavior of WeightsClass, including the batched,
 * real-allocation-history repetition-weight computation.
 */
class WeightsClassTest extends TestCase
{
    use RefreshDatabase;

    private const ACADEMIC_YEAR = '2020-2021-01';

    private const PRIOR_ACADEMIC_YEAR = '2020-2021-02';

    private function createAllocationHistory(string $taEmail, string $moduleId, string $academicYear, int $times): void
    {
        for ($i = 0; $i < $times; $i++) {
            Allocation::factory()->create([
                'academic_year' => $academicYear,
                'allocation_id' => $academicYear.'-A-01',
                'ta_id' => $taEmail,
                'module_id' => $moduleId,
            ]);
        }
    }

    public function test_calculate_repetition_weights_for_pairs_returns_empty_array_for_no_pairs(): void
    {
        $weights = app(WeightsClass::class)->calculateRepetitionWeightsForPairs(self::ACADEMIC_YEAR, []);

        $this->assertSame([], $weights);
    }

    public function test_calculate_repetition_weights_for_pairs_returns_zero_when_no_allocation_history(): void
    {
        $ta = User::factory()->graduateTa()->create();
        $module = Module::factory()->create();

        $weights = app(WeightsClass::class)->calculateRepetitionWeightsForPairs(
            self::ACADEMIC_YEAR,
            [['ta_email' => $ta->email, 'module_id' => $module->module_id]],
        );

        $this->assertSame(0, $weights[$ta->email][$module->module_id]);
    }

    public function test_calculate_repetition_weights_for_pairs_returns_tiered_weight_for_historical_allocation_counts(): void
    {
        $ta = User::factory()->graduateTa()->create();
        $modules = Module::factory()->count(6)->create();
        $timesPerModule = [1, 2, 3, 4, 5, 7];
        // Mirrors the tiers asserted in test_get_one_module_repetition_weight_tiers, capped at "5+".
        $expectedWeights = [10, 10, 20, 30, 50, 50];

        $pairs = [];
        foreach ($modules as $index => $module) {
            $this->createAllocationHistory($ta->email, $module->module_id, self::PRIOR_ACADEMIC_YEAR, $timesPerModule[$index]);
            $pairs[] = ['ta_email' => $ta->email, 'module_id' => $module->module_id];
        }

        $weights = app(WeightsClass::class)->calculateRepetitionWeightsForPairs(self::ACADEMIC_YEAR, $pairs);

        foreach ($modules as $index => $module) {
            $this->assertSame($expectedWeights[$index], $weights[$ta->email][$module->module_id]);
        }
    }

    public function test_calculate_repetition_weights_for_pairs_excludes_allocations_from_the_current_academic_year(): void
    {
        $ta = User::factory()->graduateTa()->create();
        $module = Module::factory()->create();

        // History in the current academic year is this run's own allocation, not a repeat.
        $this->createAllocationHistory($ta->email, $module->module_id, self::ACADEMIC_YEAR, 3);

        $weights = app(WeightsClass::class)->calculateRepetitionWeightsForPairs(
            self::ACADEMIC_YEAR,
            [['ta_email' => $ta->email, 'module_id' => $module->module_id]],
        );

        $this->assertSame(0, $weights[$ta->email][$module->module_id]);
    }

    public function test_calculate_repetition_weights_for_pairs_uses_one_batched_query_regardless_of_pair_count(): void
    {
        $tas = User::factory()->graduateTa()->count(5)->create();
        $modules = Module::factory()->count(5)->create();

        $pairs = [];
        foreach ($tas as $index => $ta) {
            $module = $modules[$index];
            $this->createAllocationHistory($ta->email, $module->module_id, self::PRIOR_ACADEMIC_YEAR, 2);
            $pairs[] = ['ta_email' => $ta->email, 'module_id' => $module->module_id];
        }

        DB::enableQueryLog();
        app(WeightsClass::class)->calculateRepetitionWeightsForPairs(self::ACADEMIC_YEAR, $pairs);
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // One batched `allocations` count query, plus one memoized weight-tier config lookup —
        // neither scales with the number of pairs.
        $this->assertLessThanOrEqual(2, $queryCount, "Expected a small, pair-count-independent number of queries, got {$queryCount}.");
    }

    public function test_get_one_module_repetition_weight_tiers(): void
    {
        $weightsClass = app(WeightsClass::class);

        $this->assertSame(0, $weightsClass->getOneModuleRepetitionWeight(0));
        $this->assertSame(10, $weightsClass->getOneModuleRepetitionWeight(1));
        $this->assertSame(10, $weightsClass->getOneModuleRepetitionWeight(2));
        $this->assertSame(20, $weightsClass->getOneModuleRepetitionWeight(3));
        $this->assertSame(30, $weightsClass->getOneModuleRepetitionWeight(4));
        $this->assertSame(50, $weightsClass->getOneModuleRepetitionWeight(5));
        // Capped at the "5+" tier.
        $this->assertSame(50, $weightsClass->getOneModuleRepetitionWeight(99));
    }

    public function test_get_weight_for_module_priority_tiers(): void
    {
        $weightsClass = app(WeightsClass::class);

        $this->assertSame(0, $weightsClass->getWeightForModulePriority(0));
        $this->assertSame(27, $weightsClass->getWeightForModulePriority(1));
        $this->assertSame(23, $weightsClass->getWeightForModulePriority(2));
        $this->assertSame(2, $weightsClass->getWeightForModulePriority(10));
        // Capped at the "10+" tier.
        $this->assertSame(2, $weightsClass->getWeightForModulePriority(99));
    }

    public function test_get_weight_for_one_language_priority(): void
    {
        $weightsClass = app(WeightsClass::class);

        $this->assertSame(50, $weightsClass->getWeightForOneLanguagePriority(1));
        $this->assertSame(30, $weightsClass->getWeightForOneLanguagePriority(2));
        $this->assertSame(0, $weightsClass->getWeightForOneLanguagePriority(999));
    }
}
