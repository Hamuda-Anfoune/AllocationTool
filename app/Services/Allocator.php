<?php

namespace App\Services;

use App\Exceptions\ModuleHasNoPreferencesException;
use App\Models\AcademicYear;
use App\Models\ModulePreference;
use App\Models\ModuleRankOrderList;
use App\Models\TaLanguageChoice;
use App\Models\TaPreference;
use App\Models\UsedLanguage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Allocator
{
    public function __construct(
        protected AllocationsClass $allocationsClass,
        protected WeightsClass $weightsClass,
    ) {}

    /**
     * Assigns a TA to their preferred modules up to capacity, bumping out lower-weight TAs when a module is full.
     *
     * @param  array<string, mixed>  $ta
     * @param  array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>, removed_tas: array<int, mixed>}  $allocationsMatrix
     * @return array{ta_allocations: array<string, array<string, mixed>>, module_allocations: array<string, array<string, mixed>>, removed_tas: array<int, mixed>}
     *
     * @throws ModuleHasNoPreferencesException if one of the TA's chosen modules has no submitted preferences for the current academic year
     */
    public function allocate(array $ta, array $allocationsMatrix): array
    {
        $academicYear = AcademicYear::currentYear();

        $currentTaId = $ta['ta_id'];

        $allTasPrefsAndROLs = $this->allocationsClass->createTasRolsAndPrefsForYear($academicYear);
        $modulesPrefsAndROLs = $this->allocationsClass->createFinalRolsForModulesForYear($academicYear);

        // Check if max working hours is reached, or max number of modules would be exceeded — if so, skip this TA.
        if (($allocationsMatrix['ta_allocations'][$currentTaId]['contact_hours'] >= $ta['max_contact_hours'])
            || ($allocationsMatrix['ta_allocations'][$currentTaId]['marking_hours'] >= $ta['max_marking_hours'])
            || ($ta['max_modules'] < (count($allocationsMatrix['ta_allocations'][$currentTaId]['modules']) + 1))) {
            return $allocationsMatrix;
        }

        for ($i = 1; $i <= count($ta['modules']); $i++) {
            $moduleChoiceId = $ta['modules'][$i]['module_id'];

            if (in_array($moduleChoiceId, $allocationsMatrix['ta_allocations'][$currentTaId]['modules'])
                || ($allocationsMatrix['ta_allocations'][$currentTaId]['contact_hours'] + $modulesPrefsAndROLs[$moduleChoiceId]['contact_hours']) > $ta['max_contact_hours']
                || ($allocationsMatrix['ta_allocations'][$currentTaId]['marking_hours'] + $modulesPrefsAndROLs[$moduleChoiceId]['marking_hours']) > $ta['max_marking_hours']
                || $ta['max_modules'] < (count($allocationsMatrix['ta_allocations'][$currentTaId]['modules']) + 1)) {
                // Skip this module — allocating it would exceed the TA's max working hours or max modules.
                continue;
            }

            if (! array_key_exists($moduleChoiceId, $modulesPrefsAndROLs)) {
                throw new ModuleHasNoPreferencesException($moduleChoiceId);
            }

            // If there are unallocated positions (fewer allocated TAs than no_of_assistants required)
            if (count($allocationsMatrix['module_allocations'][$moduleChoiceId]['tas']) < $modulesPrefsAndROLs[$moduleChoiceId]['no_of_assistants']) {
                $taWeightForModule = $this->allocationsClass->getTaWeightForModuleForCurrentSemester($currentTaId, $moduleChoiceId);

                $allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'][] = ['ta_id' => $currentTaId, 'weight' => $taWeightForModule];
                $allocationsMatrix['ta_allocations'][$currentTaId]['modules'][] = $moduleChoiceId;
                $allocationsMatrix['ta_allocations'][$currentTaId]['contact_hours'] += $modulesPrefsAndROLs[$moduleChoiceId]['contact_hours'];
                $allocationsMatrix['ta_allocations'][$currentTaId]['marking_hours'] += $modulesPrefsAndROLs[$moduleChoiceId]['marking_hours'];

                continue;
            }

            // Module is full — only bump a TA if the current TA outranks the lowest-weight TA already allocated.
            if (! array_key_exists($currentTaId, $modulesPrefsAndROLs[$moduleChoiceId]['tas'])) {
                continue;
            }

            // Remove the TA with the least weight from the allocation.
            $minWeight = min(array_column($allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'], 'weight'));
            $keyToRemove = array_search($minWeight, array_column($allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'], 'weight'));
            $taIdToRemove = $allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'][$keyToRemove]['ta_id'];

            unset($allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'][$keyToRemove]);

            $moduleKeyToRemove = array_search($moduleChoiceId, $allocationsMatrix['ta_allocations'][$taIdToRemove]['modules']);
            unset($allocationsMatrix['ta_allocations'][$taIdToRemove]['modules'][$moduleKeyToRemove]);

            $allocationsMatrix['ta_allocations'][$currentTaId]['contact_hours'] -= $modulesPrefsAndROLs[$moduleChoiceId]['contact_hours'];
            $allocationsMatrix['ta_allocations'][$currentTaId]['marking_hours'] -= $modulesPrefsAndROLs[$moduleChoiceId]['marking_hours'];

            // Allocate the current TA and module to each other.
            $currentTaWeightForCurrentModule = $modulesPrefsAndROLs[$moduleChoiceId]['tas'][$currentTaId]['weight'];
            $allocationsMatrix['module_allocations'][$moduleChoiceId]['tas'][$keyToRemove] = ['weight' => $currentTaWeightForCurrentModule, 'ta_id' => $currentTaId];
            $allocationsMatrix['ta_allocations'][$currentTaId]['modules'][] = $moduleChoiceId;
            $allocationsMatrix['ta_allocations'][$currentTaId]['contact_hours'] += $modulesPrefsAndROLs[$moduleChoiceId]['contact_hours'];
            $allocationsMatrix['ta_allocations'][$currentTaId]['marking_hours'] += $modulesPrefsAndROLs[$moduleChoiceId]['marking_hours'];

            if (! in_array($allTasPrefsAndROLs[$taIdToRemove]['ta_id'], array_column($allocationsMatrix['removed_tas'], 'ta_id'))) {
                $allocationsMatrix['removed_tas'][] = $allTasPrefsAndROLs[$taIdToRemove];
            }
        }

        return $allocationsMatrix;
    }

    /**
     * Recomputes and persists module_rank_order_lists rows for the current academic year.
     *
     * @throws QueryException
     */
    public function createModuleROLs(): true
    {
        $currentAcademicYear = AcademicYear::currentYear();

        if (DB::table('module_rank_order_lists')->where('academic_year', $currentAcademicYear)->exists()) {
            DB::table('module_rank_order_lists')->where('academic_year', $currentAcademicYear)->delete();
        }

        $modulesWithPrefs = ModulePreference::query()->joinModule()->forYear($currentAcademicYear)->get([
            'module_preferences.module_id',
            'module_preferences.no_of_assistants',
            'module_preferences.no_of_contact_hours',
            'module_preferences.no_of_marking_hours',
            'modules.module_name',
        ]);
        $tasWithPrefs = TaPreference::forYear($currentAcademicYear)->orderBy('max_modules')
            ->get(['ta_email', 'preference_id', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'have_tier4_visa']);

        try {
            foreach ($modulesWithPrefs as $module) {
                $moduleUsedLanguages = UsedLanguage::query()->withLanguageName()->forModuleForYear($module->module_id, $currentAcademicYear)
                    ->orderBy('used_languages.priority')
                    ->get(['used_languages.language_id', 'used_languages.priority', 'languages.language_name as Language_name']);

                foreach ($tasWithPrefs as $ta) {
                    $taId = $ta->ta_email;

                    $taRankDetails = new ModuleRankOrderList([
                        'module_id' => $module->module_id,
                        'academic_year' => $currentAcademicYear,
                        'ta_email' => $taId,
                        'ta_total_weight' => 0,
                        'did_before_weight' => 0,
                        'languages_similarity_weight' => 0,
                    ]);

                    $currentTaWithCurrentModule = DB::table('ta_module_choices')
                        ->select('priority', 'did_before')
                        ->where('preference_id', $ta->preference_id)
                        ->where('module_id', $module->module_id)
                        ->first();

                    if ($currentTaWithCurrentModule !== null) {
                        if ($currentTaWithCurrentModule->did_before) {
                            $didBeforeWeight = $this->weightsClass->calculateRepetitionWeightForModuleForTa($taId, $module->module_id);

                            $taRankDetails->did_before_weight = $didBeforeWeight;
                            $taRankDetails->ta_total_weight += $didBeforeWeight;
                        }

                        $taRankDetails->module_priority_for_ta = $currentTaWithCurrentModule->priority;
                        $taRankDetails->module_priority_for_ta_weight = $this->weightsClass->getWeightForModulePriority($currentTaWithCurrentModule->priority);
                        $taRankDetails->ta_total_weight += $this->weightsClass->getWeightForModulePriority($currentTaWithCurrentModule->priority);
                    }

                    // Weight for having programming languages in common between the TA and the module.
                    $taLanguageChoices = TaLanguageChoice::query()->withLanguageName()
                        ->where('ta_language_choices.preference_id', $ta->preference_id)
                        ->get(['ta_language_choices.language_id', 'languages.language_name']);

                    foreach ($moduleUsedLanguages as $language) {
                        foreach ($taLanguageChoices as $taLanguageChoice) {
                            if ($language->language_id === $taLanguageChoice->language_id) {
                                $languagePriorityWeight = $this->weightsClass->getWeightForOneLanguagePriority($language->priority);

                                $taRankDetails->ta_total_weight += $languagePriorityWeight;
                                $taRankDetails->languages_similarity_weight += $languagePriorityWeight;
                            }
                        }
                    }

                    $taRankDetails->save();
                }
            }
        } catch (QueryException $queryException) {
            DB::table('module_rank_order_lists')->where('academic_year', $currentAcademicYear)->delete();

            throw $queryException;
        }

        return true;
    }
}
