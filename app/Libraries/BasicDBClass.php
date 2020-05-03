<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;
use App\Academic_year;

/**
    * ONLY USE TO READ BASIC PRIMARY DATA FROM DB!. SHOULD NOT USE ANY OTHER CUSTOM CLASS.
    * (ACADEMIC YEARS, US|ERS, MODULES, TAs AND PREFERENCES DATA)
    *
    * @method string getCurrentAcademicYear()
    *
    * @method collection getModulesWithPrefsForYear(string $academic_year)
    *
    * @method collection getTAsWithPrefsForYear(string $academic_year)
    *
    * @method collection getAllModulesForYear(string $academic_year)
    *
    * @method collection getUsedLanguagesForModuleForYear(string $module_id, string $academic_year)
    *
    * @method collection getTaLanguageChoicesForYear(string $preference_id)
    *
    *
 */
class BasicDBClass
{
    /**
     * Returns all system account types
     */
    function getAccountTypes()
    {
        return DB::table('account_types')->select('account_type_id', 'account_type')->get();
    }

    function getCurrentAcademicYear()
    {
        $row = DB::table('Academic_years')->select('year')->where('current', '=', 1)->first();
        return $row->year;
    }

    function getAllAcademicYears()
    {
        return DB::table('Academic_years')->where('current', '=', 0)->get();
        //  return Academic_year::all();
    }

    /**
     * returns all modules in target academic year.
     *
     * @param  string $academic_year
     * @return array Modal: Module
     */
    function getAllModulesForYear(string $academic_year)
    {
        return DB::table('modules')->where('academic_year', '=', $academic_year)->get();
    }

    /**
     * Will return all modules with submitted preferences for year
     * @param string $academic_year
     * @return collection
     */
    function getModulesWithPrefsForYear(string $academic_year)
    {
        return DB::table('module_preferences')->select('module_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours')->where('academic_year', '=', $academic_year)->get();
    }

    /**
     * Will return all modules without preferences for year
     * @param string $academic_year
     * @return collection
     */
    function getModulesWithoutPrefsForYear(string $academic_year)
    {
        return DB::table('modules')
                    ->select('module_id', 'module_name')
                    ->where('academic_year','=',$academic_year)
                    ->whereNotExists(function($query)
                    {
                        $query->select(DB::raw(1))
                            ->from('module_preferences')
                            ->whereRaw('module_preferences.module_id = modules.module_id');
                    })
                    ->get();
    }

    /**
     * Will return all active TAs
     * @return collection
     */
    function getAllActiveTas()
    {
        return DB::table('users')
                    ->where('active', '=', 1)
                    ->where(function($q) {
                        $q->where('account_type_id', 003)
                          ->orWhere('account_type_id', 004);
                    })
                    ->get();
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string $academic_year
     * @return array of TAs
     * @return prepereties: $ta_email string
     * @return prepereties: $preference_id string
     */
    function getTAsWithPrefsForYear(string $academic_year)
    {
        return DB::table('ta_preferences')
                    ->select('ta_email', 'preference_id', 'max_contact_hours', 'max_marking_hours', 'max_modules', 'have_tier4_visa')
                    ->where('academic_year', '=', $academic_year)
                    ->orderBy('max_modules', 'ASC')
                    ->get();
    }

    /**
     * Will return all TAs without preferences for year
     * @param string $academic_year
     * @return collection
     */
    function getActiveTasWithoutPrefsForYear(string $academic_year)
    {
        return DB::table('users')
                    ->select('email', 'name')
                    ->where('active', '=', 1)
                    ->where(function($q) {
                        $q->where('account_type_id', 003)
                          ->orWhere('account_type_id', 004);
                    })
                    ->whereNotExists(function($query)use($academic_year)
                    {
                        $query->select(DB::raw(1))
                            ->from('ta_preferences')
                            ->whereRaw('ta_preferences.ta_email = users.email')
                            ->where('academic_year','=',$academic_year);
                        })
                    ->get();
    }


    function getPreferenceIdForTaForYear(string $ta_id, string $academic_year)
    {
        return DB::table('ta_preferences')
                    ->select('preference_id')
                    ->where('ta_email','=',$ta_id)
                    ->where('academic_year','=',$academic_year)
                    ->first()
                    ->preference_id;
    }

    /**
     * Return the languages a module used in specific academic year.
     *
     * @param  string $module_id
     * @param  string $academic_year
     * @return array of Used_modules
     * @return prepereties: $language_id string
     * @return prepereties: $priority int
     */
    function getUsedLanguagesForModuleForYear(string $module_id, string $academic_year)
    {
        return DB::table('used_langauges')
                    ->select('language_id', 'priority')
                    ->where('academic_year', '=', $academic_year)
                    ->where('module_id', '=', $module_id)
                    ->get();
    }

    /**
     * Return the languages a TA chose as preferences in specific academic year.
     *
     * @param  string $preference_id
     * @return array of ta_language_choices
     * @return prepereties: $language_id string
     */
    function getTaLanguageChoicesForYear(string $preference_id)
    {
        return DB::table('ta_language_choices')
                    ->select('language_id')
                    ->where('preference_id', '=', $preference_id)
                    ->get();
    }

    /**
     *      CONVENORS
     */

     function getAllActiveConvenors()
     {
        return DB::table('users')
                ->select('email','name')
                ->where('account_type_id','=', 002)
                ->where('active','=', 1)
                ->get();
     }
}
