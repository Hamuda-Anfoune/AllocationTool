<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AccountType;
use App\Models\Module;
use App\Models\ModulePreference;
use App\Models\TaLanguageChoice;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use App\Models\UniversityUser;
use App\Models\UsedLanguage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only query helper for basic primary data (academic years, users, modules, TAs and preferences).
 * Should not depend on any of the other Services classes.
 */
class BasicDBClass
{
    public function getAccountTypesWithoutTypes(): Collection
    {
        return AccountType::where('account_type_id', '!=', '000')->get(['account_type_id', 'account_type']);
    }

    public function getCurrentAcademicYear(): string
    {
        return AcademicYear::where('current', true)->value('year');
    }

    public function getAllAcademicYears(): Collection
    {
        return AcademicYear::get(['year', 'current']);
    }

    public function getAllModulesForConvenorForYear(string $convenorId, string $academicYear): Collection
    {
        return Module::where('convenor_email', $convenorId)
            ->orderBy('module_name')
            ->get(['module_id', 'module_name']);
    }

    /**
     * @return Collection<int, Module>
     */
    public function getAllModulesForYear(string $academicYear): Collection
    {
        return Module::orderBy('module_name')->get();
    }

    public function getModulesWithPrefsForYear(string $academicYear): Collection
    {
        return ModulePreference::query()
            ->join('modules', 'modules.module_id', '=', 'module_preferences.module_id')
            ->where('module_preferences.academic_year', $academicYear)
            ->get([
                'module_preferences.module_id',
                'module_preferences.no_of_assistants',
                'module_preferences.no_of_contact_hours',
                'module_preferences.no_of_marking_hours',
                'modules.module_name',
            ]);
    }

    public function getBasicPrefsForModuleForYear(string $moduleId, string $academicYear): Collection
    {
        return ModulePreference::query()
            ->join('modules', 'modules.module_id', '=', 'module_preferences.module_id')
            ->where('module_preferences.academic_year', $academicYear)
            ->where('module_preferences.module_id', $moduleId)
            ->get([
                'module_preferences.module_id',
                'module_preferences.no_of_assistants',
                'module_preferences.no_of_contact_hours',
                'module_preferences.no_of_marking_hours',
                'module_preferences.academic_year',
                'modules.module_name',
            ]);
    }

    public function getModulesWithoutPrefsForYear(string $academicYear): Collection
    {
        return Module::query()
            ->whereNotExists(function ($query) use ($academicYear) {
                $query->select(DB::raw(1))
                    ->from('module_preferences')
                    ->whereColumn('module_preferences.module_id', 'modules.module_id')
                    ->where('academic_year', $academicYear);
            })
            ->get(['module_id', 'module_name']);
    }

    public function getConvenorsWithoutPrefsForYear(string $academicYear): Collection
    {
        return Module::query()
            ->select('convenor_email')
            ->distinct()
            ->whereNotExists(function ($query) use ($academicYear) {
                $query->select(DB::raw(1))
                    ->from('module_preferences')
                    ->whereColumn('module_preferences.module_id', 'modules.module_id')
                    ->where('academic_year', $academicYear);
            })
            ->get();
    }

    public function getModulesWithoutPrefsForConvenorForYear(string $convenorId, string $academicYear): Collection
    {
        return Module::where('convenor_email', $convenorId)
            ->whereNotExists(function ($query) use ($academicYear) {
                $query->select(DB::raw(1))
                    ->from('module_preferences')
                    ->whereColumn('module_preferences.module_id', 'modules.module_id')
                    ->where('academic_year', $academicYear);
            })
            ->get();
    }

    public function getConvenorsWithoutPrefsWithModulesForYear(string $academicYear): Collection
    {
        $convenors = $this->getConvenorsWithoutPrefsForYear($academicYear);

        $names = User::whereIn('email', $convenors->pluck('convenor_email'))->pluck('name', 'email');

        foreach ($convenors as $convenor) {
            $convenor->name = $names->get($convenor->convenor_email);
            $convenor->modules = $this->getModulesWithoutPrefsForConvenorForYear($convenor->convenor_email, $academicYear);
        }

        return $convenors;
    }

    public function getAllActiveTas(): Collection
    {
        return User::query()
            ->join('account_types', 'account_types.account_type_id', '=', 'users.account_type_id')
            ->where('users.active', true)
            ->whereIn('users.account_type_id', ['003', '004'])
            ->get(['users.*', 'account_types.account_type']);
    }

    public function getTAsWithPrefsForYear(string $academicYear): Collection
    {
        return TaPreference::where('academic_year', $academicYear)
            ->orderBy('max_modules')
            ->get(['ta_email', 'preference_id', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'have_tier4_visa']);
    }

    public function getAllPrefsForTAEmail(string $email): Collection
    {
        return TaPreference::where('ta_email', $email)->get();
    }

    public function getActiveTasWithoutPrefsForYear(string $academicYear): Collection
    {
        return User::query()
            ->join('account_types', 'account_types.account_type_id', '=', 'users.account_type_id')
            ->where('users.active', true)
            ->whereIn('users.account_type_id', ['003', '004'])
            ->whereNotExists(function ($query) use ($academicYear) {
                $query->select(DB::raw(1))
                    ->from('ta_preferences')
                    ->whereColumn('ta_preferences.ta_email', 'users.email')
                    ->where('academic_year', $academicYear);
            })
            ->get(['users.email', 'users.name', 'users.account_type_id', 'account_types.account_type']);
    }

    public function getPreferenceIdForTaForYear(string $taId, string $academicYear): string
    {
        return TaPreference::where('ta_email', $taId)
            ->where('academic_year', $academicYear)
            ->value('preference_id');
    }

    public function getUsedLanguagesForModuleForYear(string $moduleId, string $academicYear): Collection
    {
        return UsedLanguage::query()
            ->join('languages', 'languages.language_id', '=', 'used_languages.language_id')
            ->where('used_languages.academic_year', $academicYear)
            ->where('used_languages.module_id', $moduleId)
            ->orderBy('used_languages.priority')
            ->get(['used_languages.language_id', 'used_languages.priority', 'languages.language_name as Language_name']);
    }

    public function getTaLanguageChoicesForPreference(string $preferenceId): Collection
    {
        return TaLanguageChoice::query()
            ->join('languages', 'languages.language_id', '=', 'ta_language_choices.language_id')
            ->where('ta_language_choices.preference_id', $preferenceId)
            ->get(['ta_language_choices.language_id', 'languages.language_name']);
    }

    public function getModuleChoicesForTAForYear(string $preferenceId): Collection
    {
        return TaModuleChoice::query()
            ->join('modules', 'modules.module_id', '=', 'ta_module_choices.module_id')
            ->where('ta_module_choices.preference_id', $preferenceId)
            ->orderBy('ta_module_choices.priority')
            ->get(['ta_module_choices.module_id', 'modules.module_name', 'ta_module_choices.priority', 'ta_module_choices.did_before']);
    }

    public function getTAPreferenceData(string $preferenceId): Collection
    {
        return TaPreference::where('preference_id', $preferenceId)->get();
    }

    public function getAllActiveConvenors(): Collection
    {
        return User::where('account_type_id', '002')
            ->where('active', true)
            ->get(['email', 'name', 'created_at']);
    }

    public function getAllactiveAdmins(): Collection
    {
        return User::where('active', true)
            ->whereIn('account_type_id', ['000', '001'])
            ->get(['email', 'name', 'created_at']);
    }

    public function getAllUniversityUsers(): Collection
    {
        return UniversityUser::query()
            ->join('account_types', 'account_types.account_type_id', '=', 'university_users.account_type_id')
            ->orderBy('university_users.account_type_id')
            ->get(['university_users.email', 'university_users.account_type_id', 'university_users.created_at', 'account_types.account_type']);
    }

    public function getAllActiveRegisteredUsers(): Collection
    {
        return User::where('active', true)
            ->orderBy('account_type_id')
            ->get(['name', 'account_type_id', 'email', 'active', 'created_at']);
    }

    public function getAllRegisteredUsers(): Collection
    {
        return User::query()
            ->join('account_types', 'account_types.account_type_id', '=', 'users.account_type_id')
            ->orderBy('users.account_type_id')
            ->get(['users.name', 'users.account_type_id', 'users.email', 'users.active', 'users.created_at', 'account_types.account_type']);
    }
}
