<?php

namespace App\Services;

use App\Models\LanguageWeight;
use App\Models\ModulePriorityWeight;
use App\Models\ModuleRepetitionWeight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only helper for the configurable weights used by the allocation algorithm.
 * Should not depend on any of the other Services classes.
 */
class WeightsClass
{
    /**
     * @var array<string, mixed>|null
     */
    protected ?array $moduleRepetitionWeightsCache = null;

    /**
     * @var array<int, int>
     */
    protected array $modulePriorityWeightCache = [];

    /**
     * @var array<int, int>
     */
    protected array $languagePriorityWeightCache = [];

    /**
     * @return array<string, mixed>|null
     */
    protected function currentModuleRepetitionWeights(): ?array
    {
        return $this->moduleRepetitionWeightsCache ??= ModuleRepetitionWeight::where('type', 'current')->first()?->only([
            'repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5',
        ]);
    }

    /**
     * Batch-computes repetition weights for a set of (ta, module) pairs from real allocation
     * history, excluding the current academic year. Pairs with no prior allocation get weight 0.
     *
     * @param  array<int, array{ta_email: string, module_id: string}>  $pairs
     * @return array<string, array<string, int>> weight[taEmail][moduleId]
     */
    public function calculateRepetitionWeightsForPairs(string $currentAcademicYear, array $pairs): array
    {
        if ($pairs === []) {
            return [];
        }

        $taEmails = array_unique(array_column($pairs, 'ta_email'));
        $moduleIds = array_unique(array_column($pairs, 'module_id'));

        $counts = DB::table('allocations')
            ->select('ta_id', 'module_id', DB::raw('COUNT(*) as times'))
            ->where('academic_year', '!=', $currentAcademicYear)
            ->whereIn('ta_id', $taEmails)
            ->whereIn('module_id', $moduleIds)
            ->groupBy('ta_id', 'module_id')
            ->get();

        $countsByTaAndModule = [];
        foreach ($counts as $count) {
            $countsByTaAndModule[$count->ta_id][$count->module_id] = (int) $count->times;
        }

        $weights = [];
        foreach ($pairs as $pair) {
            $times = $countsByTaAndModule[$pair['ta_email']][$pair['module_id']] ?? 0;
            $weights[$pair['ta_email']][$pair['module_id']] = $this->getOneModuleRepetitionWeight($times);
        }

        return $weights;
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

        $key = min($priority, 10);

        return $this->modulePriorityWeightCache[$key] ??= (function () use ($key) {
            $weights = ModulePriorityWeight::where('type', 'current')->first();

            return $weights->{'module_weight_'.$key} ?? 0;
        })();
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
        return $this->languagePriorityWeightCache[$languagePriority] ??= LanguageWeight::where('order', $languagePriority)
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
