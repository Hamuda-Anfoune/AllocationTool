<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;
use App\Academic_year;
use PhpParser\NodeVisitor\FirstFindingVisitor;

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
        return DB::table('Academic_years')->select('year')->get();
        //  return Academic_year::all();
    }

    /**
     *  ----------------------------
     *
     *            MODULES
     *
     *  ----------------------------
     */

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
        $modules = DB::table('module_preferences')
                    ->select('module_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours')
                    ->where('academic_year', '=', $academic_year)
                    ->get();

        foreach($modules as $module)
        {
            $module->module_name = DB::table('modules')
                                        ->select('module_name')
                                        ->where('module_id', '=', $module->module_id)
                                        ->first()
                                        ->module_name;
        }

        return $modules;
    }


    function getBasicPrefsForModuleForYear(string $module_id, string $academic_year)
    {
        $modules = DB::table('module_preferences')
                ->select('module_id', 'no_of_assistants', 'no_of_contact_hours', 'no_of_marking_hours', 'academic_year')
                ->where('academic_year', '=', $academic_year)
                ->where('module_id', '=', $module_id)
                ->get();

        foreach($modules as $module)
        {
            $module->module_name = DB::table('modules')
                                        ->select('module_name')
                                        ->where('module_id', '=', $module_id)
                                        ->first()
                                        ->module_name;
        }

        return $modules;
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
     *  ----------------------------
     *
     *            MODULES
     *
     *  ----------------------------
     */


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
        $languages = DB::table('used_langauges')
                            ->select('language_id', 'priority')
                            ->where('academic_year', '=', $academic_year)
                            ->where('module_id', '=', $module_id)
                            ->orderBy('priority', 'ASC')
                            ->get();

        foreach($languages as $language)
        {
            $language->Language_name = DB::table('languages')
                                            ->select('language_name')
                                            ->where('language_id','=',$language->language_id)
                                            ->first()
                                            ->language_name;
        }

        return $languages;
    }


    /**
     * Return the languages a TA chose as preferences in specific academic year.
     *
     * @param  string $preference_id
     * @return array of ta_language_choices
     * @return prepereties: $language_id string
     */
    function getTaLanguageChoicesForPreference(string $preference_id)
    {
        $language_choices = DB::table('ta_language_choices')
                    ->select('language_id')
                    ->where('preference_id', '=', $preference_id)
                    ->get();

        foreach($language_choices as $language)
        {
            $language->language_name = DB::table('languages')->select('language_name')->where('language_id', '=', $language->language_id)->first()->language_name;
        }

        return $language_choices;
    }

    /**
     * Returns module choices for TA for a specific year
     *
     * @param acdemic_year
     */
    function getModuleChoicesForTAForYear(string $preference_id)
    {
        // Get the academic year for that preference
        $target_academic_year = DB::table('ta_preferences')
                                    ->select('academic_year')
                                    ->where('preference_id', '=', $preference_id)
                                    ->first()
                                    ->academic_year;


        $current_module_choices_without_names = DB::table('ta_module_choices')
                                                    ->select('module_id','priority','did_before')
                                                    ->where('preference_id','=',$preference_id)
                                                    ->orderBy('priority','ASC')
                                                    ->get();

        // DONE: Get an array of the chosen modules' based on preference_id in ta_module_choices, add priority to array elements
        $current_module_choices = [];
        for($i=0; $i<count($current_module_choices_without_names); $i++)
        {
            $current_module_choices[$i] = DB::table('modules')
                                                        ->select('module_name')
                                                        ->where('module_id','=', $current_module_choices_without_names[$i]->module_id)
                                                        ->where('academic_year','=', $target_academic_year)
                                                        ->first();

            $current_module_choices[$i]->module_id = $current_module_choices_without_names[$i]->module_id;
            $current_module_choices[$i]->priority = $current_module_choices_without_names[$i]->priority;
            $current_module_choices[$i]->did_before = $current_module_choices_without_names[$i]->did_before;
        }

        return $current_module_choices;
    }

    function getTAPreferenceData($preference_id)
    {
        return DB::table('ta_preferences')->where('preference_id','=', $preference_id)->get();
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
