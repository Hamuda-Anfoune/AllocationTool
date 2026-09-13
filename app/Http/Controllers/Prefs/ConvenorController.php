<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Services\BasicDBClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConvenorController extends Controller
{
    public function __construct(protected BasicDBClass $basicDBClass) {}

    /**
     * The signed-in convenor's modules for the current academic year, split into those with and without submitted preferences.
     */
    public function index(Request $request): JsonResponse
    {
        $email = $request->user()->email;
        $currentAcademicYear = $this->basicDBClass->getCurrentAcademicYear();

        $preferencedConvenorModules = Module::query()
            ->where('modules.convenor_email', $email)
            ->where('modules.academic_year', $currentAcademicYear)
            ->whereExists(function ($query) {
                $query->selectRaw(1)
                    ->from('module_preferences')
                    ->whereColumn('module_preferences.module_id', 'modules.module_id');
            })
            ->get();

        $nonpreferencedConvenorModules = $this->basicDBClass->getModulesWithoutPrefsForConvenorForYear($email, $currentAcademicYear);

        return response()->json([
            'current_academic_year' => $currentAcademicYear,
            'preferenced_modules' => ModuleResource::collection($preferencedConvenorModules),
            'nonpreferenced_modules' => ModuleResource::collection($nonpreferencedConvenorModules),
        ]);
    }
}
