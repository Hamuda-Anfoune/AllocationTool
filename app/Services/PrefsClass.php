<?php

namespace App\Services;

use App\Models\ModulePreference;
use App\Models\TaLanguageChoice;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use App\Models\UsedLanguage;
use Illuminate\Database\QueryException;

/**
 * Creates/clears TA-preference and module-preference records (and their nested choices).
 * Takes plain validated arrays rather than the request/session directly, so it stays decoupled from HTTP concerns.
 */
class PrefsClass
{
    public function taPreferenceExists(string $preferenceId): bool
    {
        return TaPreference::where('preference_id', $preferenceId)->exists();
    }

    public function modulePreferenceExists(string $moduleId, string $academicYear): bool
    {
        return ModulePreference::where('module_id', $moduleId)->where('academic_year', $academicYear)->exists();
    }

    public function clearTaLanguageChoices(string $preferenceId): int
    {
        return TaLanguageChoice::where('preference_id', $preferenceId)->delete();
    }

    public function clearTaModuleChoices(string $preferenceId): int
    {
        return TaModuleChoice::where('preference_id', $preferenceId)->delete();
    }

    public function clearTaPreference(string $preferenceId): int
    {
        return TaPreference::where('preference_id', $preferenceId)->delete();
    }

    /**
     * Stores a TA's submitted preference, plus up to 10 module choices and 7 language choices.
     *
     * @param  array<string, mixed>  $data  validated request data
     *
     * @throws QueryException
     */
    public function storeTaPreferences(array $data, string $email, string $preferenceId): bool
    {
        TaPreference::create([
            'preference_id' => $preferenceId,
            'ta_email' => $email,
            'max_modules' => $data['max_modules'],
            'max_contact_hours' => $data['max_contact_hours'],
            'max_marking_hours' => $data['max_marking_hours'],
            'academic_year' => $data['academic_year'],
            'have_tier4_visa' => (bool) ($data['have_tier4_visa'] ?? false),
        ]);

        $priority = 1;
        for ($i = 1; $i <= 10; $i++) {
            $moduleId = $data['module_'.$i.'_id'] ?? null;

            if ($moduleId === null) {
                continue;
            }

            TaModuleChoice::create([
                'preference_id' => $preferenceId,
                'ta_email' => $email,
                'module_id' => $moduleId,
                'priority' => $priority,
                'did_before' => (bool) ($data['done_before_'.$i] ?? false),
            ]);

            $priority++;
        }

        for ($i = 1; $i <= 7; $i++) {
            $languageId = $data['preferred_language_'.$i.'_id'] ?? null;

            if ($languageId === null) {
                continue;
            }

            TaLanguageChoice::create([
                'preference_id' => $preferenceId,
                'language_id' => $languageId,
            ]);
        }

        return true;
    }

    /**
     * Stores a module's submitted preference, plus up to 7 used-language choices.
     *
     * @param  array<string, mixed>  $data  validated request data
     *
     * @throws QueryException
     */
    public function storeModulePreferences(array $data): bool
    {
        ModulePreference::create([
            'module_id' => $data['module_id'],
            'no_of_assistants' => abs((int) ceil($data['no_of_assistants'])),
            'no_of_contact_hours' => abs((int) ceil($data['no_of_contact_hours'])),
            'no_of_marking_hours' => abs((int) ceil($data['no_of_marking_hours'])),
            'academic_year' => $data['academic_year'],
        ]);

        $priority = 1;
        for ($i = 1; $i <= 7; $i++) {
            $languageId = $data['language_'.$i.'_id'] ?? null;

            if ($languageId === null) {
                continue;
            }

            UsedLanguage::create([
                'module_id' => $data['module_id'],
                'language_id' => $languageId,
                'academic_year' => $data['academic_year'],
                'priority' => $priority,
            ]);

            $priority++;
        }

        return true;
    }
}
