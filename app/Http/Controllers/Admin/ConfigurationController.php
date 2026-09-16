<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLanguageWeightsRequest;
use App\Http\Requests\Admin\UpdateModulePriorityWeightsRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use App\Services\WeightsClass;
use Illuminate\Http\JsonResponse;

class ConfigurationController extends Controller
{
    public function __construct(protected WeightsClass $weightsClass) {}

    /**
     * The configuration dashboard: current weights and academic years.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'current_academic_year' => AcademicYear::currentYear(),
            'academic_years' => AcademicYearResource::collection(AcademicYear::get(['year', 'current'])),
            'module_priority_weights' => $this->weightsClass->getWeightsForAllModulePriorities()->first(),
            'module_repetition_weights' => $this->weightsClass->getAllCurrentModuleRepetitionWeights()->first(),
            'language_weights' => $this->weightsClass->getWeightForAllLanguagePriorities(),
        ]);
    }

    public function updateModulePriorityWeights(UpdateModulePriorityWeightsRequest $request): JsonResponse
    {
        $this->weightsClass->updateModulePriorityWeights($request->validated());

        return response()->json(['message' => 'Module priority weights updated.']);
    }

    public function resetModulePriorityWeights(): JsonResponse
    {
        $this->weightsClass->resetModulePriorityWeights();

        return response()->json(['message' => 'Module priority weights reset to their defaults.']);
    }

    public function updateLanguageWeights(UpdateLanguageWeightsRequest $request): JsonResponse
    {
        $this->weightsClass->updateLanguageWeights($request->validated());

        return response()->json(['message' => 'Language weights updated.']);
    }

    public function resetLanguageWeights(): JsonResponse
    {
        $this->weightsClass->resetLanguageWeights();

        return response()->json(['message' => 'Language weights reset to their defaults.']);
    }
}
