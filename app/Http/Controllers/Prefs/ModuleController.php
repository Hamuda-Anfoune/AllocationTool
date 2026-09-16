<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prefs\StoreModulePreferenceRequest;
use App\Http\Requests\Prefs\UpdateModulePreferenceRequest;
use App\Http\Resources\ModuleResource;
use App\Models\AcademicYear;
use App\Models\Module;
use App\Services\AllocationsClass;
use App\Services\BasicDBClass;
use App\Services\PrefsClass;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    public function __construct(
        protected BasicDBClass $basicDBClass,
        protected AllocationsClass $allocationsClass,
        protected PrefsClass $prefsClass,
    ) {}

    /**
     * Modules with/without submitted preferences for a year (defaults to the current academic year).
     */
    public function index(Request $request): JsonResponse
    {
        $academicYear = $request->query('academic_year') ?? $this->basicDBClass->getCurrentAcademicYear();

        return response()->json([
            'academic_year' => $academicYear,
            'academic_years' => $this->basicDBClass->getAllAcademicYears(),
            'modules_with_preferences' => $this->basicDBClass->getModulesWithPrefsForYear($academicYear),
            'modules_without_preferences' => ModuleResource::collection($this->basicDBClass->getModulesWithoutPrefsForYear($academicYear)),
        ]);
    }

    public function store(StoreModulePreferenceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $module = Module::findOrFail($data['module_id']);
        Gate::authorize('submitPreferences', $module);

        if ($this->prefsClass->modulePreferenceExists($data['module_id'], $data['academic_year'])) {
            return response()->json([
                'message' => "Preference already submitted for {$data['module_id']} for this academic year.",
            ], 409);
        }

        try {
            $this->prefsClass->storeModulePreferences($data);
        } catch (QueryException) {
            return response()->json(['message' => 'Error saving the preferences, please try again.'], 500);
        }

        return response()->json(['message' => 'Preference saved.'], 201);
    }

    /**
     * A convenor gets an editable view of their own module's current-year preferences; everyone else gets a read-only view.
     */
    public function show(Request $request, Module $module, AcademicYear $academicYear): JsonResponse
    {
        $currentAcademicYear = $this->basicDBClass->getCurrentAcademicYear();

        $editable = $academicYear->year === $currentAcademicYear
            && $request->user()->can('update', $module);

        return response()->json([
            'editable' => $editable,
            'academic_year' => $academicYear->year,
            'module_basic_preferences' => $this->basicDBClass->getBasicPrefsForModuleForYear($module->module_id, $academicYear->year),
            'module_language_choices' => $this->basicDBClass->getUsedLanguagesForModuleForYear($module->module_id, $academicYear->year),
        ]);
    }

    public function update(UpdateModulePreferenceRequest $request, Module $module, AcademicYear $academicYear): JsonResponse
    {
        if (! $this->prefsClass->modulePreferenceExists($module->module_id, $academicYear->year)) {
            return response()->json(['message' => 'This module did not submit preferences for this academic year.'], 404);
        }

        DB::table('used_languages')->where('academic_year', $academicYear->year)->where('module_id', $module->module_id)->delete();
        DB::table('module_preferences')->where('academic_year', $academicYear->year)->where('module_id', $module->module_id)->delete();

        $data = array_merge($request->validated(), [
            'module_id' => $module->module_id,
            'academic_year' => $academicYear->year,
        ]);

        try {
            $this->prefsClass->storeModulePreferences($data);
        } catch (QueryException) {
            return response()->json(['message' => 'Error saving the preferences, please try again.'], 500);
        }

        return response()->json(['message' => 'Preference updated.']);
    }

    public function destroy(Module $module, AcademicYear $academicYear): JsonResponse
    {
        if ($this->allocationsClass->allocationExistsForYear($academicYear->year)) {
            return response()->json([
                'message' => 'Sorry, TA roles have already been allocated for this semester — preferences cannot be deleted.',
            ], 409);
        }

        DB::table('used_languages')->where('module_id', $module->module_id)->where('academic_year', $academicYear->year)->delete();
        DB::table('module_preferences')->where('module_id', $module->module_id)->where('academic_year', $academicYear->year)->delete();

        return response()->json([
            'message' => "Preferences for {$module->module_id} for semester {$academicYear->year} have been deleted.",
        ]);
    }
}
