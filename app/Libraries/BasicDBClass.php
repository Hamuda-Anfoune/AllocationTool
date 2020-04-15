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

    function getAllModulesForYear(string $academic_year)
    {
        return DB::table('modules')->where('academic_year', '=', $academic_year)->get();
    }

}
