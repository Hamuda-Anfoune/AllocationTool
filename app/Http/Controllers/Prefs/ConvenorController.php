<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\AcademicYear;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConvenorController extends Controller
{
    /**
     * The signed-in convenor's modules for the current academic year, split into those with and without submitted preferences.
     */
    public function index(Request $request): JsonResponse
    {
        $email = $request->user()->email;
        $currentAcademicYear = AcademicYear::currentYear();

        $preferencedConvenorModules = Module::query()
            ->where('modules.convenor_email', $email)
            ->where('modules.academic_year', $currentAcademicYear)
            ->whereExists(function ($query) {
                $query->selectRaw(1)
                    ->from('module_preferences')
                    ->whereColumn('module_preferences.module_id', 'modules.module_id');
            })
            ->get();

        $nonpreferencedConvenorModules = Module::where('convenor_email', $email)->withoutPreferencesForYear($currentAcademicYear)->get();

        return response()->json([
            'current_academic_year' => $currentAcademicYear,
            'preferenced_modules' => ModuleResource::collection($preferencedConvenorModules),
            'nonpreferenced_modules' => ModuleResource::collection($nonpreferencedConvenorModules),
        ]);
    }
}
