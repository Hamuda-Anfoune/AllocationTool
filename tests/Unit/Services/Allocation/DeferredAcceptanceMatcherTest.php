<?php

namespace Tests\Unit\Services\Allocation;

use App\Exceptions\ModuleHasNoPreferencesException;
use App\Services\Allocation\DeferredAcceptanceMatcher;
use Tests\TestCase;

/**
 * Pure, no-DB tests for the deferred-acceptance matcher: capacity/budget limits,
 * bump-by-weight, permanent vs. budget-infeasible rejection, many-to-many holding,
 * termination, order independence and tie-break determinism.
 */
class DeferredAcceptanceMatcherTest extends TestCase
{
    private function matcher(): DeferredAcceptanceMatcher
    {
        return new DeferredAcceptanceMatcher;
    }

    /**
     * @param  array<int, string>  $moduleIdsInPriorityOrder
     * @return array{ta_id: string, max_contact_hours: int, max_marking_hours: int, max_modules: int, modules: array<int, array{module_id: string}>}
     */
    private function ta(string $taId, int $maxModules, int $maxContactHours, int $maxMarkingHours, array $moduleIdsInPriorityOrder): array
    {
        return [
            'ta_id' => $taId,
            'max_contact_hours' => $maxContactHours,
            'max_marking_hours' => $maxMarkingHours,
            'max_modules' => $maxModules,
            'modules' => array_map(static fn (string $moduleId) => ['module_id' => $moduleId], $moduleIdsInPriorityOrder),
        ];
    }

    /**
     * @return array{no_of_assistants: int, contact_hours: int, marking_hours: int}
     */
    private function module(int $capacity, int $contactHours, int $markingHours): array
    {
        return [
            'no_of_assistants' => $capacity,
            'contact_hours' => $contactHours,
            'marking_hours' => $markingHours,
        ];
    }

    public function test_max_modules_limits_how_many_modules_a_ta_can_hold(): void
    {
        $tas = ['ta1' => $this->ta('ta1', maxModules: 1, maxContactHours: 100, maxMarkingHours: 100, moduleIdsInPriorityOrder: ['A', 'B'])];
        $modules = ['A' => $this->module(5, 1, 1), 'B' => $this->module(5, 1, 1)];
        $weights = [
            'A' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 1]],
            'B' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 2]],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['ta1']['modules']);
    }

    public function test_max_contact_hours_limits_allocation(): void
    {
        $tas = ['ta1' => $this->ta('ta1', maxModules: 5, maxContactHours: 3, maxMarkingHours: 100, moduleIdsInPriorityOrder: ['A', 'B'])];
        $modules = ['A' => $this->module(5, 2, 0), 'B' => $this->module(5, 2, 0)];
        $weights = [
            'A' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 1]],
            'B' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 2]],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['ta1']['modules']);
    }

    public function test_max_marking_hours_limits_allocation(): void
    {
        $tas = ['ta1' => $this->ta('ta1', maxModules: 5, maxContactHours: 100, maxMarkingHours: 3, moduleIdsInPriorityOrder: ['A', 'B'])];
        $modules = ['A' => $this->module(5, 0, 2), 'B' => $this->module(5, 0, 2)];
        $weights = [
            'A' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 1]],
            'B' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 2]],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['ta1']['modules']);
    }

    public function test_budget_infeasible_module_is_skipped_but_scan_continues_within_the_same_pass(): void
    {
        $tas = ['ta1' => $this->ta('ta1', maxModules: 5, maxContactHours: 3, maxMarkingHours: 100, moduleIdsInPriorityOrder: ['A', 'B'])];
        $modules = ['A' => $this->module(5, 5, 0), 'B' => $this->module(5, 2, 0)];
        $weights = [
            'A' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 1]],
            'B' => ['ta1' => ['weight' => 10.0, 'module_priority_for_ta' => 2]],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        // A is too expensive and is skipped without stopping the scan; the cheaper B is still taken.
        $this->assertSame(['B'], $result['ta_allocations']['ta1']['modules']);
    }

    public function test_module_bumps_by_weight_regardless_of_proposal_order(): void
    {
        $modules = ['A' => $this->module(1, 1, 1)];
        $weights = [
            'A' => [
                'low' => ['weight' => 30.0, 'module_priority_for_ta' => 1],
                'mid' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
                'high' => ['weight' => 80.0, 'module_priority_for_ta' => 1],
            ],
        ];
        $low = $this->ta('low', 1, 100, 100, ['A']);
        $mid = $this->ta('mid', 1, 100, 100, ['A']);
        $high = $this->ta('high', 1, 100, 100, ['A']);

        foreach ([['low' => $low, 'mid' => $mid, 'high' => $high], ['high' => $high, 'mid' => $mid, 'low' => $low]] as $tasInOrder) {
            $result = $this->matcher()->run($tasInOrder, $modules, $weights);

            $this->assertSame(['A'], $result['ta_allocations']['high']['modules']);
            $this->assertSame([], $result['ta_allocations']['mid']['modules']);
            $this->assertSame([], $result['ta_allocations']['low']['modules']);
            $this->assertCount(1, $result['module_allocations']['A']['tas']);
            $this->assertSame('high', $result['module_allocations']['A']['tas'][0]['ta_id']);
        }
    }

    public function test_lower_weight_proposer_is_permanently_rejected_and_scan_continues_to_next_module(): void
    {
        $tas = [
            'holder' => $this->ta('holder', 1, 100, 100, ['A']),
            'loser' => $this->ta('loser', 2, 100, 100, ['A', 'B']),
        ];
        $modules = ['A' => $this->module(1, 1, 1), 'B' => $this->module(1, 1, 1)];
        $weights = [
            'A' => [
                'holder' => ['weight' => 80.0, 'module_priority_for_ta' => 1],
                'loser' => ['weight' => 20.0, 'module_priority_for_ta' => 1],
            ],
            'B' => [
                'loser' => ['weight' => 40.0, 'module_priority_for_ta' => 2],
            ],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['holder']['modules']);
        $this->assertSame(['B'], $result['ta_allocations']['loser']['modules']);
    }

    public function test_ta_reconsiders_earlier_preference_after_being_bumped_frees_budget(): void
    {
        $tas = [
            'x' => $this->ta('x', 2, 5, 100, ['A', 'B']),
            'y' => $this->ta('y', 1, 10, 100, ['A']),
        ];
        $modules = ['A' => $this->module(1, 5, 0), 'B' => $this->module(5, 3, 0)];
        $weights = [
            'A' => [
                'x' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
                'y' => ['weight' => 80.0, 'module_priority_for_ta' => 1],
            ],
            'B' => [
                'x' => ['weight' => 40.0, 'module_priority_for_ta' => 2],
            ],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        // x initially takes A (using its whole 5-hour budget), leaving B unaffordable in that pass.
        // y then outweighs x on A and evicts them; freed of A's hours, x re-scans and takes B.
        $this->assertSame(['B'], $result['ta_allocations']['x']['modules']);
        $this->assertSame(['A'], $result['ta_allocations']['y']['modules']);
    }

    public function test_many_to_many_one_ta_can_hold_multiple_modules_and_one_module_can_hold_multiple_tas(): void
    {
        $tas = [
            'ta1' => $this->ta('ta1', 2, 100, 100, ['A', 'B']),
            'ta2' => $this->ta('ta2', 1, 100, 100, ['A']),
        ];
        $modules = ['A' => $this->module(2, 1, 1), 'B' => $this->module(5, 1, 1)];
        $weights = [
            'A' => [
                'ta1' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
                'ta2' => ['weight' => 40.0, 'module_priority_for_ta' => 1],
            ],
            'B' => [
                'ta1' => ['weight' => 50.0, 'module_priority_for_ta' => 2],
            ],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertEqualsCanonicalizing(['A', 'B'], $result['ta_allocations']['ta1']['modules']);
        $this->assertSame(['A'], $result['ta_allocations']['ta2']['modules']);
        $this->assertCount(2, $result['module_allocations']['A']['tas']);
    }

    public function test_termination_completes_without_hanging_for_a_bump_chain_heavy_scenario(): void
    {
        $tas = [];
        $weights = ['A' => []];

        for ($i = 1; $i <= 20; $i++) {
            $taId = "t{$i}";
            $tas[$taId] = $this->ta($taId, 1, 100, 100, ['A']);
            $weights['A'][$taId] = ['weight' => (float) $i, 'module_priority_for_ta' => 1];
        }

        $modules = ['A' => $this->module(1, 1, 1)];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['t20']['modules']);

        foreach (range(1, 19) as $i) {
            $this->assertSame([], $result['ta_allocations']["t{$i}"]['modules']);
        }
    }

    public function test_result_is_independent_of_initial_ta_queue_order(): void
    {
        $tas = [
            'T1' => $this->ta('T1', 1, 100, 100, ['M1', 'M2']),
            'T2' => $this->ta('T2', 1, 100, 100, ['M1', 'M3']),
            'T3' => $this->ta('T3', 1, 100, 100, ['M2', 'M3']),
            'T4' => $this->ta('T4', 1, 100, 100, ['M3', 'M1']),
        ];
        $modules = ['M1' => $this->module(1, 1, 1), 'M2' => $this->module(1, 1, 1), 'M3' => $this->module(2, 1, 1)];
        $weights = [
            'M1' => [
                'T1' => ['weight' => 90.0, 'module_priority_for_ta' => 1],
                'T2' => ['weight' => 80.0, 'module_priority_for_ta' => 1],
                'T4' => ['weight' => 20.0, 'module_priority_for_ta' => 2],
            ],
            'M2' => [
                'T1' => ['weight' => 10.0, 'module_priority_for_ta' => 2],
                'T3' => ['weight' => 70.0, 'module_priority_for_ta' => 1],
            ],
            'M3' => [
                'T2' => ['weight' => 50.0, 'module_priority_for_ta' => 2],
                'T3' => ['weight' => 60.0, 'module_priority_for_ta' => 2],
                'T4' => ['weight' => 40.0, 'module_priority_for_ta' => 1],
            ],
        ];

        $forward = $this->matcher()->run($tas, $modules, $weights);
        $reversed = $this->matcher()->run(array_reverse($tas, true), $modules, $weights);

        $this->assertSame($this->normalize($forward), $this->normalize($reversed));
    }

    public function test_tie_break_resolves_by_module_priority_for_ta_on_exact_weight_tie(): void
    {
        $tas = [
            'taB' => $this->ta('taB', 1, 100, 100, ['A']),
            'taA' => $this->ta('taA', 1, 100, 100, ['A']),
        ];
        $modules = ['A' => $this->module(1, 1, 1)];
        $weights = [
            'A' => [
                'taA' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
                'taB' => ['weight' => 50.0, 'module_priority_for_ta' => 2],
            ],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        // Equal weight: taA ranked the module higher (priority 1 vs 2) and wins, evicting taB
        // even though taB was dequeued and accepted into the empty module first.
        $this->assertSame(['A'], $result['ta_allocations']['taA']['modules']);
        $this->assertSame([], $result['ta_allocations']['taB']['modules']);
    }

    public function test_tie_break_falls_back_to_ta_email_when_weight_and_priority_are_equal(): void
    {
        $tas = [
            'zoe@example.com' => $this->ta('zoe@example.com', 1, 100, 100, ['A']),
            'amy@example.com' => $this->ta('amy@example.com', 1, 100, 100, ['A']),
        ];
        $modules = ['A' => $this->module(1, 1, 1)];
        $weights = [
            'A' => [
                'amy@example.com' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
                'zoe@example.com' => ['weight' => 50.0, 'module_priority_for_ta' => 1],
            ],
        ];

        $result = $this->matcher()->run($tas, $modules, $weights);

        $this->assertSame(['A'], $result['ta_allocations']['amy@example.com']['modules']);
        $this->assertSame([], $result['ta_allocations']['zoe@example.com']['modules']);
    }

    public function test_throws_module_has_no_preferences_exception_when_module_choice_has_no_rol_entry(): void
    {
        $tas = ['ta1' => $this->ta('ta1', 1, 100, 100, ['GHOST'])];

        $this->expectException(ModuleHasNoPreferencesException::class);

        $this->matcher()->run($tas, [], []);
    }

    /**
     * @param  array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>}  $result
     * @return array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>}
     */
    private function normalize(array $result): array
    {
        $taAllocations = $result['ta_allocations'];
        foreach ($taAllocations as &$ta) {
            sort($ta['modules']);
        }

        $moduleAllocations = $result['module_allocations'];
        foreach ($moduleAllocations as &$module) {
            usort($module['tas'], static fn ($a, $b) => $a['ta_id'] <=> $b['ta_id']);
        }

        ksort($taAllocations);
        ksort($moduleAllocations);

        return ['ta_allocations' => $taAllocations, 'module_allocations' => $moduleAllocations];
    }
}
