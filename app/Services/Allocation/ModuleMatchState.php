<?php

namespace App\Services\Allocation;

/**
 * Mutable per-run state for one module during deferred-acceptance matching.
 */
final class ModuleMatchState
{
    /** @var array<string, array{weight: float, module_priority_for_ta: int}> ta_id => weight row, currently holding */
    public array $holders = [];

    public function __construct(
        public readonly string $moduleId,
        public readonly int $capacity,
        public readonly int $contactHours,
        public readonly int $markingHours,
    ) {}

    public function hasSpareCapacity(): bool
    {
        return count($this->holders) < $this->capacity;
    }

    /**
     * @param  array{weight: float, module_priority_for_ta: int}  $weightRow
     */
    public function accept(string $taId, array $weightRow): void
    {
        $this->holders[$taId] = $weightRow;
    }

    public function evict(string $taId): void
    {
        unset($this->holders[$taId]);
    }

    /**
     * Finds the current holder that loses to every other holder under the given comparator.
     *
     * @param  callable(array, string, array, string): int  $compare  Returns <0 when the first TA outranks the second.
     * @return array{0: string, 1: array{weight: float, module_priority_for_ta: int}}
     */
    public function lowestHolder(callable $compare): array
    {
        $lowestTaId = null;
        $lowestRow = null;

        foreach ($this->holders as $taId => $row) {
            if ($lowestTaId === null || $compare($row, $taId, $lowestRow, $lowestTaId) > 0) {
                $lowestTaId = $taId;
                $lowestRow = $row;
            }
        }

        return [$lowestTaId, $lowestRow];
    }
}
