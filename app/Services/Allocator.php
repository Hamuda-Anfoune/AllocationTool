<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ModulePreference;
use App\Models\TaPreference;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Allocator
{
    public function __construct(
        protected WeightsClass $weightsClass,
    ) {}

    /**
     * Recomputes and persists module_rank_order_lists rows for the current academic year.
     *
     * @throws QueryException
     */
    public function createModuleROLs(): true
    {
        $currentAcademicYear = AcademicYear::currentYear();

        DB::table('module_rank_order_lists')->where('academic_year', $currentAcademicYear)->delete();

        $modulesWithPrefs = ModulePreference::query()->joinModule()->forYear($currentAcademicYear)->get([
            'module_preferences.module_id',
            'module_preferences.no_of_assistants',
            'module_preferences.no_of_contact_hours',
            'module_preferences.no_of_marking_hours',
            'modules.module_name',
        ]);
        $tasWithPrefs = TaPreference::forYear($currentAcademicYear)->orderBy('max_modules')
            ->get(['ta_email', 'preference_id', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'have_tier4_visa']);

        $moduleIds = $modulesWithPrefs->pluck('module_id');
        $preferenceIds = $tasWithPrefs->pluck('preference_id');

        // Batch-prefetch everything the double loop below needs, keyed for O(1) lookup,
        // instead of running a module_choices/language_choices query per (module, TA) pair.
        $choicesByPreferenceAndModule = DB::table('ta_module_choices')
            ->select('preference_id', 'module_id', 'priority')
            ->whereIn('preference_id', $preferenceIds)
            ->whereIn('module_id', $moduleIds)
            ->get()
            ->groupBy(fn ($choice) => $choice->preference_id.'|'.$choice->module_id);

        $taEmailByPreferenceId = $tasWithPrefs->pluck('ta_email', 'preference_id');

        $repetitionPairs = $choicesByPreferenceAndModule
            ->map(fn ($choices) => $choices->first())
            ->filter(fn ($choice) => $taEmailByPreferenceId->has($choice->preference_id))
            ->map(fn ($choice) => ['ta_email' => $taEmailByPreferenceId->get($choice->preference_id), 'module_id' => $choice->module_id])
            ->values()
            ->all();

        $repetitionWeights = $this->weightsClass->calculateRepetitionWeightsForPairs($currentAcademicYear, $repetitionPairs);

        $languageIdsByPreference = DB::table('ta_language_choices')
            ->select('preference_id', 'language_id')
            ->whereIn('preference_id', $preferenceIds)
            ->get()
            ->groupBy('preference_id')
            ->map(fn ($choices) => $choices->pluck('language_id')->all());

        $usedLanguagesByModule = DB::table('used_languages')
            ->select('module_id', 'language_id', 'priority')
            ->whereIn('module_id', $moduleIds)
            ->where('academic_year', $currentAcademicYear)
            ->orderBy('priority')
            ->get()
            ->groupBy('module_id');

        $now = now();
        $rows = [];

        try {
            foreach ($modulesWithPrefs as $module) {
                $moduleUsedLanguages = $usedLanguagesByModule->get($module->module_id, collect());

                foreach ($tasWithPrefs as $ta) {
                    $taId = $ta->ta_email;

                    $taTotalWeight = 0.0;
                    $didBeforeWeight = 0.0;
                    $modulePriorityForTa = 0;
                    $modulePriorityForTaWeight = 0.0;
                    $languagesSimilarityWeight = 0.0;

                    $currentTaWithCurrentModule = $choicesByPreferenceAndModule->get($ta->preference_id.'|'.$module->module_id)?->first();

                    if ($currentTaWithCurrentModule !== null) {
                        $didBeforeWeight = $repetitionWeights[$taId][$module->module_id] ?? 0;
                        $taTotalWeight += $didBeforeWeight;

                        $modulePriorityForTa = $currentTaWithCurrentModule->priority;
                        $modulePriorityForTaWeight = $this->weightsClass->getWeightForModulePriority($currentTaWithCurrentModule->priority);
                        $taTotalWeight += $modulePriorityForTaWeight;
                    }

                    // Weight for having programming languages in common between the TA and the module.
                    $taLanguageIds = $languageIdsByPreference->get($ta->preference_id, []);

                    foreach ($moduleUsedLanguages as $language) {
                        if (in_array($language->language_id, $taLanguageIds, true)) {
                            $languagePriorityWeight = $this->weightsClass->getWeightForOneLanguagePriority($language->priority);

                            $taTotalWeight += $languagePriorityWeight;
                            $languagesSimilarityWeight += $languagePriorityWeight;
                        }
                    }

                    $rows[] = [
                        'module_id' => $module->module_id,
                        'academic_year' => $currentAcademicYear,
                        'ta_email' => $taId,
                        'ta_total_weight' => $taTotalWeight,
                        'did_before_weight' => $didBeforeWeight,
                        'module_priority_for_ta' => $modulePriorityForTa,
                        'module_priority_for_ta_weight' => $modulePriorityForTaWeight,
                        'languages_similarity_weight' => $languagesSimilarityWeight,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('module_rank_order_lists')->insert($chunk);
            }
        } catch (QueryException $queryException) {
            DB::table('module_rank_order_lists')->where('academic_year', $currentAcademicYear)->delete();

            throw $queryException;
        }

        return true;
    }
}
