<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\ModulePreference;
use App\Models\TaAllocationData;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Builds and queries the allocation matrix/ROLs used by the Allocator, and manages persisted allocations.
 * Only used by AllocationController.
 */
class AllocationsClass
{
    /**
     * Returns an associative array of module ROLs for a specific year, keyed by module_id.
     *
     * Loads every ranked TA per module (no top-N truncation) in a single query for the
     * whole year, since a module's bump eligibility must consider its full ranking, not
     * just a `no_of_assistants`-sized shortlist.
     *
     * @return array<string, array{no_of_assistants: int, contact_hours: int, marking_hours: float, tas: array<string, array{weight: mixed, ta_id: string, ta_priority: int}>}>
     */
    public function createFinalRolsForModulesForYear(string $academicYear): array
    {
        $modulesROLs = [];

        $allModulesWithPrefs = ModulePreference::query()->joinModule()->forYear($academicYear)->get([
            'module_preferences.module_id',
            'module_preferences.no_of_assistants',
            'module_preferences.no_of_contact_hours',
            'module_preferences.no_of_marking_hours',
            'modules.module_name',
        ]);

        $rolsByModule = DB::table('module_rank_order_lists')
            ->select('module_id', 'ta_email', 'ta_total_weight')
            ->whereIn('module_id', $allModulesWithPrefs->pluck('module_id'))
            ->where('academic_year', $academicYear)
            ->orderByDesc('ta_total_weight')
            ->get()
            ->groupBy('module_id');

        foreach ($allModulesWithPrefs as $module) {
            $tas = [];
            $taPriority = 1;

            foreach ($rolsByModule->get($module->module_id, collect()) as $ta) {
                $tas[$ta->ta_email] = [
                    'weight' => $ta->ta_total_weight,
                    'ta_id' => $ta->ta_email,
                    'ta_priority' => $taPriority,
                ];

                $taPriority++;
            }

            $modulesROLs[$module->module_id] = [
                'no_of_assistants' => $module->no_of_assistants,
                'contact_hours' => $module->no_of_contact_hours,
                'marking_hours' => ceil($module->no_of_marking_hours / $module->no_of_assistants),
                'tas' => $tas,
            ];
        }

        return $modulesROLs;
    }

    /**
     * Returns the full module_rank_order_lists weight table for a year, keyed by module_id then ta_email.
     *
     * @return array<string, array<string, array{weight: float, module_priority_for_ta: int}>>
     */
    public function loadWeightsForYear(string $academicYear): array
    {
        $weights = [];

        $rows = DB::table('module_rank_order_lists')
            ->select('module_id', 'ta_email', 'ta_total_weight', 'module_priority_for_ta')
            ->where('academic_year', $academicYear)
            ->get();

        foreach ($rows as $row) {
            $weights[$row->module_id][$row->ta_email] = [
                'weight' => (float) $row->ta_total_weight,
                'module_priority_for_ta' => (int) $row->module_priority_for_ta,
            ];
        }

        return $weights;
    }

    /**
     * Returns a matrix of TAs with their preferences and arrays of all their preferred modules and priorities.
     *
     * @return array<string, array{ta_id: string, max_contact_hours: int, max_marking_hours: int, max_modules: int, modules: array<int, array{module_id: string}>}>
     */
    public function createTasRolsAndPrefsForYear(string $academicYear): array
    {
        $allTasPrefsAndROLs = [];

        $allTasWithPrefs = TaPreference::forYear($academicYear)->orderBy('max_modules')
            ->get(['ta_email', 'preference_id', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'have_tier4_visa']);

        $choicesByPreference = TaModuleChoice::whereIn('preference_id', $allTasWithPrefs->pluck('preference_id'))
            ->orderBy('priority')
            ->get(['preference_id', 'module_id', 'priority'])
            ->groupBy('preference_id');

        $allModuleIds = DB::table('modules')->orderBy('module_id')->pluck('module_id');

        foreach ($allTasWithPrefs as $ta) {
            $taId = $ta->ta_email;

            $preferredModulesForCurrentTa = $choicesByPreference->get($ta->preference_id, collect());
            $preferredModuleIds = $preferredModulesForCurrentTa->pluck('module_id')->all();

            $modules = [];

            foreach ($preferredModulesForCurrentTa as $module) {
                $modules[$module->priority] = ['module_id' => $module->module_id];
            }

            foreach ($allModuleIds as $moduleId) {
                if (! in_array($moduleId, $preferredModuleIds, true)) {
                    $modules[] = ['module_id' => $moduleId];
                }
            }

            $allTasPrefsAndROLs[$taId] = [
                'ta_id' => $taId,
                'max_contact_hours' => $ta->max_contact_hours,
                'max_marking_hours' => $ta->max_marking_hours,
                'max_modules' => $ta->max_modules,
                'modules' => $modules,
            ];
        }

        return $allTasPrefsAndROLs;
    }

    public function allocationExistsForYear(string $academicYear): bool
    {
        return Allocation::where('academic_year', $academicYear)->exists();
    }

    public function getAllAllocationIds(): Collection
    {
        return Allocation::select('allocation_id')->distinct()->get();
    }

    /**
     * Fetches every allocation row for the given allocation id, grouped by module.
     *
     * @return Collection<int, Collection<int, Allocation>>
     */
    public function getAllocationById(string $allocationId): Collection
    {
        return Allocation::where('allocation_id', $allocationId)
            ->get(['allocation_id', 'ta_id', 'module_id', 'academic_year', 'creator_email', 'created_at', 'updated_at'])
            ->groupBy('module_id');
    }

    public function getTaAllocationDataById(string $allocationId): Collection
    {
        return TaAllocationData::where('allocation_id', $allocationId)->get();
    }

    public function deleteAllAllocationsForYear(string $academicYear): bool
    {
        $allocations = Allocation::where('academic_year', $academicYear)->delete();
        $taData = TaAllocationData::where('academic_year', $academicYear)->delete();

        return $allocations && $taData;
    }

    public function deleteAllocationById(string $allocationId): bool
    {
        $allocations = Allocation::where('allocation_id', $allocationId)->delete();
        $taData = TaAllocationData::where('allocation_id', $allocationId)->delete();

        return $allocations && $taData;
    }
}
