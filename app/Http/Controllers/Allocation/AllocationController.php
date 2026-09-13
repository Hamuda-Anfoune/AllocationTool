<?php

namespace App\Http\Controllers\Allocation;

use App\Exceptions\ModuleHasNoPreferencesException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllocationResource;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\TaAllocationDataResource;
use App\Http\Resources\UserResource;
use App\Models\Allocation;
use App\Models\TaAllocationData;
use App\Services\AllocationsClass;
use App\Services\Allocator;
use App\Services\BasicDBClass;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function __construct(
        protected BasicDBClass $basicDBClass,
        protected AllocationsClass $allocationsClass,
        protected Allocator $allocator,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'allocations' => $this->allocationsClass->getAllAllocationIds(),
        ]);
    }

    public function allocationData(): JsonResponse
    {
        $currentAcademicYear = $this->basicDBClass->getCurrentAcademicYear();

        return response()->json([
            'current_academic_year' => $currentAcademicYear,
            'academic_years' => $this->basicDBClass->getAllAcademicYears(),
            'university_users_count' => $this->basicDBClass->getAllUniversityUsers()->count(),
            'active_users_count' => $this->basicDBClass->getAllActiveRegisteredUsers()->count(),
            'active_admins_count' => $this->basicDBClass->getAllactiveAdmins()->count(),
            'active_tas_count' => $this->basicDBClass->getAllActiveTas()->count(),
            'active_tas_without_prefs_count' => $this->basicDBClass->getActiveTasWithoutPrefsForYear($currentAcademicYear)->count(),
            'active_convenor_count' => $this->basicDBClass->getAllActiveConvenors()->count(),
            'active_convenors_missing_prefs_count' => $this->basicDBClass->getConvenorsWithoutPrefsForYear($currentAcademicYear)->count(),
            'current_year_modules_count' => $this->basicDBClass->getAllModulesForYear($currentAcademicYear)->count(),
            'current_modules_without_prefs_count' => $this->basicDBClass->getModulesWithoutPrefsForYear($currentAcademicYear)->count(),
        ]);
    }

    /**
     * Run the allocation algorithm for the current academic year and persist the resulting allocations.
     */
    public function store(Request $request): JsonResponse
    {
        $academicYear = $this->basicDBClass->getCurrentAcademicYear();

        if ($this->allocationsClass->allocationExistsForYear($academicYear)) {
            return response()->json([
                'message' => 'TA roles have already been allocated for the current semester.',
            ], 409);
        }

        $modulesWithoutPrefs = $this->basicDBClass->getModulesWithoutPrefsForYear($academicYear);
        $tasWithoutPrefs = $this->basicDBClass->getActiveTasWithoutPrefsForYear($academicYear);

        if ($modulesWithoutPrefs->isNotEmpty() || $tasWithoutPrefs->isNotEmpty()) {
            return response()->json([
                'message' => 'Some modules or teaching assistants have not submitted preferences for the current academic year yet. See /admin/allocations/missing-preferences.',
            ], 409);
        }

        try {
            $this->allocator->createModuleROLs();
        } catch (QueryException) {
            return response()->json(['message' => 'Failed to create module rank-order-lists. Please try again later.'], 500);
        }

        $allTasPrefsAndROLs = $this->allocationsClass->createTasRolsAndPrefsForYear($academicYear);
        $allocationsMatrix = $this->allocationsClass->initiateAllocationsMatrix($academicYear);

        try {
            foreach ($allTasPrefsAndROLs as $ta) {
                $allocationsMatrix = $this->allocator->allocate($ta, $allocationsMatrix);
            }

            // Re-run allocation for any TAs bumped out of a module, bounded by the total number of active TAs.
            $maxReallocationRounds = $this->basicDBClass->getAllActiveTas()->count();

            for ($k = 0; $k <= $maxReallocationRounds; $k++) {
                if (empty($allocationsMatrix['removed_tas'])) {
                    break;
                }

                foreach ($allocationsMatrix['removed_tas'] as $key => $removed) {
                    unset($allocationsMatrix['removed_tas'][$key]);
                    $allocationsMatrix = $this->allocator->allocate($removed, $allocationsMatrix);
                }
            }
        } catch (ModuleHasNoPreferencesException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $allocationId = $academicYear.'-A-01';
        $creatorEmail = $request->user()->email;

        // Race-condition guard: re-check right before persisting.
        if ($this->allocationsClass->allocationExistsForYear($academicYear)) {
            return response()->json([
                'message' => 'TA roles have already been allocated for the current semester.',
            ], 409);
        }

        foreach ($allocationsMatrix['ta_allocations'] as $taAllocation) {
            foreach ($taAllocation['modules'] as $allocatedModule) {
                Allocation::create([
                    'allocation_id' => $allocationId,
                    'academic_year' => $academicYear,
                    'module_id' => $allocatedModule,
                    'ta_id' => $taAllocation['ta_id'],
                    'creator_email' => $creatorEmail,
                ]);
            }

            TaAllocationData::create([
                'allocation_id' => $allocationId,
                'academic_year' => $academicYear,
                'ta_id' => $taAllocation['ta_id'],
                'contact_hours' => $taAllocation['contact_hours'],
                'marking_hours' => $taAllocation['marking_hours'],
            ]);
        }

        return response()->json([
            'message' => 'Teaching assistant roles were allocated successfully.',
            'allocation_id' => $allocationId,
        ], 201);
    }

    public function show(string $allocation): JsonResponse
    {
        return response()->json([
            'allocation_id' => $allocation,
            'allocation_data' => $this->allocationsClass->getAllocationById($allocation)
                ->map(fn ($rows) => AllocationResource::collection($rows)),
            'ta_allocation_data' => TaAllocationDataResource::collection($this->allocationsClass->getTaAllocationDataById($allocation)),
        ]);
    }

    /**
     * Delete the current academic year's allocation (no `$allocation` given) or a specific one by id.
     */
    public function destroy(?string $allocation = null): JsonResponse
    {
        if ($allocation === null) {
            $academicYear = $this->basicDBClass->getCurrentAcademicYear();

            if (! $this->allocationsClass->allocationExistsForYear($academicYear)) {
                return response()->json([
                    'message' => 'No allocation found for the current semester.',
                ], 404);
            }

            $this->allocationsClass->deleteAllAllocationsForYear($academicYear);

            return response()->json(['message' => 'Allocation for the current semester was deleted successfully.']);
        }

        $this->allocationsClass->deleteAllocationById($allocation);

        return response()->json(['message' => "Allocation {$allocation} was deleted successfully."]);
    }

    public function missingPrefs(): JsonResponse
    {
        $academicYear = $this->basicDBClass->getCurrentAcademicYear();

        $modulesWithoutPrefs = $this->basicDBClass->getModulesWithoutPrefsForYear($academicYear);
        $tasWithoutPrefs = $this->basicDBClass->getActiveTasWithoutPrefsForYear($academicYear);

        return response()->json([
            'modules_without_preferences' => ModuleResource::collection($modulesWithoutPrefs),
            'tas_without_preferences' => UserResource::collection($tasWithoutPrefs),
        ]);
    }
}
