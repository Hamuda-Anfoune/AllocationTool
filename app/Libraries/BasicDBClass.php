<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;

class BasicDBClass
{
    function getCurrentAcademicYear()
    {
        $row = DB::table('Academic_years')->select('year')->where('current', '=', 1)->first();
        return $row->year;
    }

    function getModulesWithPrefsForYear(string $academic_year)
    {
        return DB::table('module_preferences')->select('module_id')->where('academic_year', '=', $academic_year)->get();
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
        return DB::table('ta_preferences')->select('ta_email', 'preference_id')->where('academic_year', '=', $academic_year)->get();
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
}
