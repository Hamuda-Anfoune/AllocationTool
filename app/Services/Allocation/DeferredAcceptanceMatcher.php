<?php

namespace App\Services\Allocation;

use App\Exceptions\ModuleHasNoPreferencesException;
use SplQueue;

/**
 * Many-to-many deferred-acceptance (Gale-Shapley-style) matcher for allocating TAs to modules.
 *
 * Each TA may hold up to `max_modules` modules, further bounded by independent
 * `max_contact_hours` and `max_marking_hours` budgets. Each module has a fixed
 * capacity (`no_of_assistants`); once full, it only accepts a proposer that
 * outranks its current worst-ranked holder, evicting that holder, who then
 * re-proposes down their own remaining preference list.
 */
final class DeferredAcceptanceMatcher
{
    /**
     * @param  array<string, array{ta_id: string, max_contact_hours: int, max_marking_hours: int, max_modules: int, modules: array<int, array{module_id: string}>}>  $tasPrefsInOrder  Key order must not affect the result.
     * @param  array<string, array{no_of_assistants: int, contact_hours: int, marking_hours: float}>  $moduleCapacities
     * @param  array<string, array<string, array{weight: float, module_priority_for_ta: int}>>  $weights  weights[moduleId][taId], full table, no top-N truncation
     * @return array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>}
     *
     * @throws ModuleHasNoPreferencesException
     */
    public function run(array $tasPrefsInOrder, array $moduleCapacities, array $weights): array
    {
        $taStates = [];
        foreach ($tasPrefsInOrder as $ta) {
            $taStates[$ta['ta_id']] = new TaMatchState(
                $ta['ta_id'],
                $ta['max_modules'],
                $ta['max_contact_hours'],
                $ta['max_marking_hours'],
                $ta['modules'],
            );
        }

        $moduleStates = [];
        foreach ($moduleCapacities as $moduleId => $module) {
            $moduleStates[$moduleId] = new ModuleMatchState(
                $moduleId,
                $module['no_of_assistants'],
                (int) $module['contact_hours'],
                (int) $module['marking_hours'],
            );
        }

        $queue = new SplQueue;
        $queued = [];

        foreach (array_keys($taStates) as $taId) {
            $queue->enqueue($taId);
            $queued[$taId] = true;
        }

        while (! $queue->isEmpty()) {
            $taId = $queue->dequeue();
            $queued[$taId] = false;
            $this->processTa($taStates[$taId], $taStates, $moduleStates, $weights, $queue, $queued);
        }

        return $this->buildResult($taStates, $moduleStates);
    }

    /**
     * @param  array<string, TaMatchState>  $taStates
     * @param  array<string, ModuleMatchState>  $moduleStates
     * @param  array<string, array<string, array{weight: float, module_priority_for_ta: int}>>  $weights
     * @param  array<string, bool>  $queued
     */
    private function processTa(TaMatchState $taState, array $taStates, array $moduleStates, array $weights, SplQueue $queue, array &$queued): void
    {
        $snapshot = $taState->remaining;

        foreach ($snapshot as $moduleId) {
            if ($taState->isBudgetExhausted()) {
                break;
            }

            if (! array_key_exists($moduleId, $moduleStates)) {
                throw new ModuleHasNoPreferencesException($moduleId);
            }

            $moduleState = $moduleStates[$moduleId];

            if ($taState->wouldExceedBudget($moduleState->contactHours, $moduleState->markingHours)) {
                continue;
            }

            $proposerRow = $weights[$moduleId][$taState->taId] ?? ['weight' => 0.0, 'module_priority_for_ta' => 0];

            if ($moduleState->hasSpareCapacity()) {
                $moduleState->accept($taState->taId, $proposerRow);
                $taState->accept($moduleId, $moduleState->contactHours, $moduleState->markingHours);

                continue;
            }

            [$lowestHolderId, $lowestHolderRow] = $moduleState->lowestHolder($this->compare(...));

            if ($this->compare($proposerRow, $taState->taId, $lowestHolderRow, $lowestHolderId) >= 0) {
                $taState->blacklist($moduleId);

                continue;
            }

            $moduleState->evict($lowestHolderId);
            $taStates[$lowestHolderId]->evict($moduleId, $moduleState->contactHours, $moduleState->markingHours);

            $moduleState->accept($taState->taId, $proposerRow);
            $taState->accept($moduleId, $moduleState->contactHours, $moduleState->markingHours);

            if (! ($queued[$lowestHolderId] ?? false)) {
                $queue->enqueue($lowestHolderId);
                $queued[$lowestHolderId] = true;
            }
        }
    }

    /**
     * Deterministic tie-break: (ta_total_weight DESC, module_priority_for_ta ASC, ta_email ASC).
     * Returns <0 when TA A outranks TA B, >0 when B outranks A. Never 0 (ta ids are unique).
     *
     * @param  array{weight: float, module_priority_for_ta: int}  $aRow
     * @param  array{weight: float, module_priority_for_ta: int}  $bRow
     */
    private function compare(array $aRow, string $aTaId, array $bRow, string $bTaId): int
    {
        if ($aRow['weight'] !== $bRow['weight']) {
            return $aRow['weight'] > $bRow['weight'] ? -1 : 1;
        }

        if ($aRow['module_priority_for_ta'] !== $bRow['module_priority_for_ta']) {
            return $aRow['module_priority_for_ta'] <=> $bRow['module_priority_for_ta'];
        }

        return $aTaId <=> $bTaId;
    }

    /**
     * @param  array<string, TaMatchState>  $taStates
     * @param  array<string, ModuleMatchState>  $moduleStates
     * @return array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>}
     */
    private function buildResult(array $taStates, array $moduleStates): array
    {
        $taAllocations = [];
        foreach ($taStates as $taId => $taState) {
            $taAllocations[$taId] = [
                'ta_id' => $taId,
                'contact_hours' => $taState->contactHoursUsed,
                'marking_hours' => $taState->markingHoursUsed,
                'modules' => array_keys($taState->held),
            ];
        }

        $moduleAllocations = [];
        foreach ($moduleStates as $moduleId => $moduleState) {
            $moduleAllocations[$moduleId] = [
                'tas' => array_map(
                    static fn (string $taId, array $row) => ['ta_id' => $taId, 'weight' => $row['weight']],
                    array_keys($moduleState->holders),
                    array_values($moduleState->holders),
                ),
            ];
        }

        return [
            'ta_allocations' => $taAllocations,
            'module_allocations' => $moduleAllocations,
        ];
    }
}
