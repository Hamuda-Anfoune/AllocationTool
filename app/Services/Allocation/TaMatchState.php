<?php

namespace App\Services\Allocation;

/**
 * Mutable per-run state for one TA during deferred-acceptance matching.
 */
final class TaMatchState
{
    /** @var array<int, string> ordered module ids not yet accepted or permanently blacklisted */
    public array $remaining;

    /** @var array<string, true> module_id => true, modules currently held */
    public array $held = [];

    public int $contactHoursUsed = 0;

    public int $markingHoursUsed = 0;

    /**
     * @param  array<int, array{module_id: string}>  $modulesInPriorityOrder
     */
    public function __construct(
        public readonly string $taId,
        public readonly int $maxModules,
        public readonly int $maxContactHours,
        public readonly int $maxMarkingHours,
        array $modulesInPriorityOrder,
    ) {
        $this->remaining = array_values(array_map(
            static fn (array $entry) => $entry['module_id'],
            $modulesInPriorityOrder,
        ));
    }

    public function isBudgetExhausted(): bool
    {
        return count($this->held) >= $this->maxModules
            || $this->contactHoursUsed >= $this->maxContactHours
            || $this->markingHoursUsed >= $this->maxMarkingHours;
    }

    public function wouldExceedBudget(int $contactHours, int $markingHours): bool
    {
        return ($this->contactHoursUsed + $contactHours) > $this->maxContactHours
            || ($this->markingHoursUsed + $markingHours) > $this->maxMarkingHours;
    }

    public function accept(string $moduleId, int $contactHours, int $markingHours): void
    {
        $this->held[$moduleId] = true;
        $this->contactHoursUsed += $contactHours;
        $this->markingHoursUsed += $markingHours;
        $this->removeFromRemaining($moduleId);
    }

    public function evict(string $moduleId, int $contactHours, int $markingHours): void
    {
        unset($this->held[$moduleId]);
        $this->contactHoursUsed -= $contactHours;
        $this->markingHoursUsed -= $markingHours;
    }

    public function blacklist(string $moduleId): void
    {
        $this->removeFromRemaining($moduleId);
    }

    private function removeFromRemaining(string $moduleId): void
    {
        $key = array_search($moduleId, $this->remaining, true);

        if ($key !== false) {
            unset($this->remaining[$key]);
        }
    }
}
