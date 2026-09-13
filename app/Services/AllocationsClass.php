<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\TaAllocationData;
use App\Models\TaModuleChoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Builds and queries the allocation matrix/ROLs used by the Allocator, and manages persisted allocations.
 * Only used by AllocationController — depends on BasicDBClass.
 */
class AllocationsClass
{
    public function __construct(protected BasicDBClass $basicDBClass) {}

    /**
     * @return array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>, removed_tas: array<int, mixed>}
     */
    public function initiateAllocationsMatrix(string $academicYear): array
    {
        $allActiveTas = $this->basicDBClass->getAllActiveTas();
        $allModules = $this->basicDBClass->getAllModulesForYear($academicYear);

        $taAllocations = [];
        foreach ($allActiveTas as $ta) {
            $taAllocations[$ta->email] = [
                'ta_id' => $ta->email,
                'weekly_working_hours' => 0,
                'contact_hours' => 0,
                'marking_hours' => 0,
                'modules' => [],
            ];
        }

        $moduleAllocations = [];
        foreach ($allModules as $module) {
            $moduleAllocations[$module->module_id] = [
                'tas' => [],
            ];
        }

        return [
            'ta_allocations' => $taAllocations,
            'module_allocations' => $moduleAllocations,
            'removed_tas' => [],
        ];
    }

    /**
     * Returns an associative array of module ROLs for a specific year, keyed by module_id.
     *
     * @return array<string, array{no_of_assistants: int, contact_hours: int, marking_hours: float, tas: array<string, array{weight: mixed, ta_id: string, ta_priority: int}>}>
     */
    public function createFinalRolsForModulesForYear(string $academicYear): array
    {
        $modulesROLs = [];

        $allModulesWithPrefs = $this->basicDBClass->getModulesWithPrefsForYear($academicYear);

        foreach ($allModulesWithPrefs as $module) {
            $topTas = DB::table('module_rank_order_lists')
                ->select('ta_email', 'ta_total_weight')
                ->where('module_id', $module->module_id)
                ->orderByDesc('ta_total_weight')
                ->take($module->no_of_assistants)
                ->get();

            $tas = [];
            $taPriority = 1;

            foreach ($topTas as $ta) {
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
     * Returns a matrix of TAs with their preferences and arrays of all their preferred modules and priorities.
     *
     * @return array<string, array{ta_id: string, max_contact_hours: int, max_marking_hours: int, max_modules: int, modules: array<int, array{module_id: string}>}>
     */
    public function createTasRolsAndPrefsForYear(string $academicYear): array
    {
        $allTasPrefsAndROLs = [];

        $allTasWithPrefs = $this->basicDBClass->getTAsWithPrefsForYear($academicYear);

        foreach ($allTasWithPrefs as $ta) {
            $taId = $ta->ta_email;

            $preferredModulesForCurrentTa = TaModuleChoice::where('ta_email', $taId)
                ->where('preference_id', $ta->preference_id)
                ->orderBy('priority')
                ->get(['module_id', 'priority']);

            $notPreferredModulesForTa = DB::table('modules')
                ->select('module_id')
                ->whereNotExists(function ($query) use ($ta) {
                    $query->select(DB::raw(1))
                        ->from('ta_module_choices')
                        ->whereColumn('ta_module_choices.module_id', 'modules.module_id')
                        ->where('ta_module_choices.preference_id', $ta->preference_id);
                })
                ->get();

            $modules = [];

            foreach ($preferredModulesForCurrentTa as $module) {
                $modules[$module->priority] = ['module_id' => $module->module_id];
            }

            foreach ($notPreferredModulesForTa as $module) {
                $modules[] = ['module_id' => $module->module_id];
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

    public function getTaWeightForModuleForCurrentSemester(string $taId, string $moduleId): int
    {
        $currentAcademicYear = $this->basicDBClass->getCurrentAcademicYear();

        return DB::table('module_rank_order_lists')
            ->where('academic_year', $currentAcademicYear)
            ->where('ta_email', $taId)
            ->where('module_id', $moduleId)
            ->value('ta_total_weight');
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
