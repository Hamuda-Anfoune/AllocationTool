<?php

namespace App\Http\Controllers\Allocation;

use App\Exceptions\ModuleHasNoPreferencesException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllocationResource;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\TaAllocationDataResource;
use App\Http\Resources\UserResource;
use App\Models\AcademicYear;
use App\Models\Allocation;
use App\Models\Module;
use App\Models\TaAllocationData;
use App\Models\UniversityUser;
use App\Models\User;
use App\Services\AllocationsClass;
use App\Services\Allocator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function __construct(
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
        $currentAcademicYear = AcademicYear::currentYear();

        return response()->json([
            'current_academic_year' => $currentAcademicYear,
            'academic_years' => AcademicYear::get(['year', 'current']),
            'university_users_count' => UniversityUser::query()->withAccountType()->count(),
            'active_users_count' => User::active()->count(),
            'active_admins_count' => User::admins()->active()->count(),
            'active_tas_count' => User::active()->tasAndGtas()->count(),
            'active_tas_without_prefs_count' => User::active()->tasAndGtas()->withoutTaPreferencesForYear($currentAcademicYear)->count(),
            'active_convenor_count' => User::convenors()->active()->count(),
            'active_convenors_missing_prefs_count' => Module::withoutPreferencesForYear($currentAcademicYear)->distinct()->count('convenor_email'),
            'current_year_modules_count' => Module::query()->count(),
            'current_modules_without_prefs_count' => Module::withoutPreferencesForYear($currentAcademicYear)->count(),
        ]);
    }

    /**
     * Run the allocation algorithm for the current academic year and persist the resulting allocations.
     */
    public function store(Request $request): JsonResponse
    {
        $academicYear = AcademicYear::currentYear();

        if ($this->allocationsClass->allocationExistsForYear($academicYear)) {
            return response()->json([
                'message' => 'TA roles have already been allocated for the current semester.',
            ], 409);
        }

        $modulesWithoutPrefs = $this->modulesWithoutPrefsForYear($academicYear);
        $tasWithoutPrefs = $this->tasWithoutPrefsForYear($academicYear);

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
            $maxReallocationRounds = User::active()->tasAndGtas()->count();

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
            $academicYear = AcademicYear::currentYear();

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
        $academicYear = AcademicYear::currentYear();

        $modulesWithoutPrefs = $this->modulesWithoutPrefsForYear($academicYear);
        $tasWithoutPrefs = $this->tasWithoutPrefsForYear($academicYear);

        return response()->json([
            'modules_without_preferences' => ModuleResource::collection($modulesWithoutPrefs),
            'tas_without_preferences' => UserResource::collection($tasWithoutPrefs),
        ]);
    }

    /**
     * @return Collection<int, Module>
     */
    private function modulesWithoutPrefsForYear(string $academicYear): Collection
    {
        return Module::withoutPreferencesForYear($academicYear)->get(['module_id', 'module_name']);
    }

    /**
     * @return Collection<int, User>
     */
    private function tasWithoutPrefsForYear(string $academicYear): Collection
    {
        return User::query()->active()->tasAndGtas()->withAccountType()->withoutTaPreferencesForYear($academicYear)
            ->get(['users.email', 'users.name', 'users.account_type_id', 'account_types.account_type']);
    }
}
