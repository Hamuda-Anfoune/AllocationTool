<?php

namespace App\Services;

use App\Models\LanguageWeight;
use App\Models\ModulePriorityWeight;
use App\Models\ModuleRepetitionWeight;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only helper for the configurable weights used by the allocation algorithm.
 * Should not depend on any of the other Services classes.
 */
class WeightsClass
{
    /**
     * @return array<string, mixed>|null
     */
    protected function currentModuleRepetitionWeights(): ?array
    {
        return ModuleRepetitionWeight::where('type', 'current')->first()?->only([
            'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
        ]);
    }

    /**
     * Will calculate how many times a TA has assisted with a module and return the equivalent weight for that.
     */
    public function calculateRepetitionWeightForModuleForTa(string $taEmail, string $moduleId): int
    {
        // TODO: calculate the actual repetition count from previous allocations where the TA was allocated to the module.
        return $this->getOneModuleRepetitionWeight(1);
    }

    public function getOneModuleRepetitionWeight(int $times): int
    {
        if ($times <= 0) {
            return 0;
        }

        $weights = $this->currentModuleRepetitionWeights();
        $key = 'repeated_times_'.min($times, 5);

        return $weights[$key] ?? 0;
    }

    public function getAllCurrentModuleRepetitionWeights(): Collection
    {
        return ModuleRepetitionWeight::where('type', 'current')->get([
            'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
        ]);
    }

    /**
     * Give the weight according to the priority of the module in the TA's ROL.
     */
    public function getWeightForModulePriority(int $priority): int
    {
        if ($priority <= 0) {
            return 0;
        }

        $weights = ModulePriorityWeight::where('type', 'current')->first();
        $key = 'module_weight_'.min($priority, 10);

        return $weights->{$key} ?? 0;
    }

    public function getWeightsForAllModulePriorities(): Collection
    {
        return ModulePriorityWeight::where('type', 'current')->get([
            'module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4', 'module_weight_5',
            'module_weight_6', 'module_weight_7', 'module_weight_8', 'module_weight_9', 'module_weight_10',
        ]);
    }

    public function getWeightForOneLanguagePriority(int $languagePriority): int
    {
        return LanguageWeight::where('order', $languagePriority)
            ->where('type', 'current')
            ->value('weight') ?? 0;
    }

    /**
     * Returns all weights for all programming-language priorities, keyed by priority order.
     *
     * @return array<int, int>
     */
    public function getWeightForAllLanguagePriorities(): array
    {
        return LanguageWeight::where('type', 'current')->get()->pluck('weight', 'order')->toArray();
    }

    /**
     * @param  array<string, int>  $weights
     */
    public function updateModulePriorityWeights(array $weights): int
    {
        return ModulePriorityWeight::where('type', 'current')->update([
            'module_weight_1' => $weights['module_priority_weight_1'],
            'module_weight_2' => $weights['module_priority_weight_2'],
            'module_weight_3' => $weights['module_priority_weight_3'],
            'module_weight_4' => $weights['module_priority_weight_4'],
            'module_weight_5' => $weights['module_priority_weight_5'],
            'module_weight_6' => $weights['module_priority_weight_6'],
            'module_weight_7' => $weights['module_priority_weight_7'],
            'module_weight_8' => $weights['module_priority_weight_8'],
            'module_weight_9' => $weights['module_priority_weight_9'],
            'module_weight_10' => $weights['module_priority_weight_10'],
        ]);
    }

    /**
     * Resets the module priority weights to the default values stored in the database.
     */
    public function resetModulePriorityWeights(): int
    {
        $default = ModulePriorityWeight::where('type', 'default')->first();

        return ModulePriorityWeight::where('type', 'current')->update($default->only([
            'module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4', 'module_weight_5',
            'module_weight_6', 'module_weight_7', 'module_weight_8', 'module_weight_9', 'module_weight_10',
        ]));
    }

    /**
     * @param  array<string, int>  $weights
     */
    public function updateLanguageWeights(array $weights): bool
    {
        for ($i = 1; $i <= 5; $i++) {
            LanguageWeight::where('type', 'current')
                ->where('order', $i)
                ->update(['weight' => $weights['language_weight_'.$i]]);
        }

        return true;
    }

    public function resetLanguageWeights(): bool
    {
        $defaults = LanguageWeight::where('type', 'default')->get(['weight', 'order']);

        foreach ($defaults as $default) {
            LanguageWeight::where('type', 'current')
                ->where('order', $default->order)
                ->update(['weight' => $default->weight]);
        }

        return true;
    }
}
