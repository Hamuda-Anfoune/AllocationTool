<?php

namespace App\Libraries;

use App\ModuleRepeatitionWeight;
use Illuminate\Support\Facades\DB;
use App\Libraries\BasicDBClass;
use App\Libraries\WeightsClass;

/**
 * NOT TO BE USED BY OTHER CLASSES. ONLY USED BY CONTROLLER.
 *
 *  @uses BasicDBClass()
 */
class AllocationsClass
{
    /**
     * Initiates the allocation matrix for modules with empty arrays of TAs
     */
    function intitiateModuleAllocationMatrix($academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        $module_allocation_matrix =[];

        // Get all Modules With prefs for year
        $all_modules = $basic_db_calss->getAllModulesForYear($academic_year);

        foreach($all_modules as $module)
        {
            // An empty array of TAs
            $tas = array();

            $module_allocation_matrix[$module->module_id] =
            [
                'tas' => $tas
            ];
        }

        return $module_allocation_matrix;
    }

    /**
     * Initiates the allocation matrix for TAs with empty arrays of modules
     */
    function intitiateTaAllocationMatrix($academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        $ta_allocation_matrix =[];

        // Get all Modules With prefs for year
        $all_active_tas = $basic_db_calss->getAllActiveTas();

        foreach($all_active_tas as $ta)
        {
            // An empty array of TAs
            $modules = array();

            $ta_allocation_matrix[$ta->ta_email] =
            [
                'modules' => $modules
            ];
        }

        return $ta_allocation_matrix;
    }

    /**
     * Returns an associative array of module ROLs for a specific year, with module_id as the keys of the array
     *
     * @param string $academic_year
     *
     * @return array key[module_id] : [int number_of_assistants, int working_hours_per_week, array tas : [int weight, string ta_email]]
     */
    function createFinalRolsForModulesForYear(string $academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        // Create the matrix of the modules' ROLs
        // Matrix initialisation
        $modules_ROLs = [];

        // Get all Modules With prefs for year
        $all_modules_with_prefs = $basic_db_calss->getModulesWithPrefsForYear($academic_year);

        foreach($all_modules_with_prefs as $module)
        {
            // Get top TAs from primary ROL based on no_of_assistants: e.g. top 3 TAs if no_of_assistants = 3
            $top_tas = DB::table('module_rank_order_lists')->select('ta_email', 'ta_total_weight')->where('module_id','=',$module->module_id)->orderBy('ta_total_weight', 'DESC')->take($module->no_of_assistants)->get();

            // Declare a clear TAs array: $tas
            $tas = [];

            // initiate priority of TAs
            $ta_priority = 1;

            // Iterate through the top TAs
            foreach($top_tas as $ta)
            {

                // Add to Array of TAs for module
                $current_ta =
                [
                    'weight' => $ta->ta_total_weight,
                    'ta_email' => $ta->ta_email,
                    'ta_priority' => $ta_priority
                ];

                $tas[$ta_priority] = $current_ta;

                // Add to priority
                $ta_priority++;
            }


            // assign module id to the entry in the matrix
            $modules_ROLs[$module->module_id] =
            [
                'no_of_assistants' => $module->no_of_assistants,
                'working_hours_per_week' => $module->no_of_contact_hours + $module->no_of_marking_hours,
                'tas'=> $tas
            ];
        }

        return $modules_ROLs;

        // TEST
        // $current_ta =
        //         [
        //             'weight' => 100,
        //             'ta_email' => 'lol'
        //         ];
        //         $tas['haha'] = $current_ta;


        // $modules_ROLs['test'] =
        // [
        //     'no_of_assistants' => 4,
        //     'tas' => $tas
        // ];

        // $ta_exists_in_Rol = (array_key_exists("some_ta_email",$modules_ROLs['test']['tas'])) ? true : false;
        // return $ta_exists_in_Rol;
        // End of test

    }

    /**
     * Returns a matrix of TAs with their preferences and arrays of all their preferred modules and their priorities.
     *
     * @param string $academic_year
     *
     * @return array key[ta_email]: {int weekly_working_hours, int max_weekly_working_hours, int max_modules, array modules: [{ key module_id: value priority}]}
     */
    function createTasRolsAndPrefsForYear(string $academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        // Create the matrix of the modules' ROLs
        // Matrix initialisation
        $all_tas_prefs_and_ROLs = [];

        // Gat all TAs with prefs
        $all_tas_with_prefs = $basic_db_calss->getTAsWithPrefsForYear($academic_year);

        foreach($all_tas_with_prefs as $ta)
        {
            // Get all modules a TA has chosen and their priorities
            $all_modules_for_current_ta = DB::table('ta_module_choices')->select('module_id', 'priority')->where('ta_email','=',$ta->ta_email)->orderBy('priority', 'ASC')->get();

            // Calculate max_working_hours based on having Tier 4 visa or not
            ($ta->have_tier4_visa == true) ? $max_weekly_working_hours = 20 :  $max_weekly_working_hours = 40;

            // Declare a clear modules array
            $modules = [];

            // Iterate through the modules
            foreach($all_modules_for_current_ta as $module)
            {
                // Add to Array of modules for current TA
                // $current_module =
                // [
                //     $module->module_id => $module->priority
                // ];

                // $modules[] = $current_module;
                $modules[$module->priority] =
                [
                    'module_id' => $module->module_id
                ];
            }

            // assign module id to the entry in the matrix
            $all_tas_prefs_and_ROLs[$ta->ta_email] =
            [
                'ta_email' => $ta->ta_email,
                'weekly_working_hours' => $ta->max_contact_hours + $ta->max_marking_hours,
                'max_weekly_working_hours' => $max_weekly_working_hours,
                'max_modules' => $ta->max_modules,
                'modules'=> $modules
            ];
        }
        return $all_tas_prefs_and_ROLs;
    }

    /**
     * Decides which TA has higher rank (weight) in module ROL
     *
     * @param string $module_id
     * @param string $ta1_email: first ta_email
     * @param string $ta2_email: second ta_email
     *
     * @return string $ta_email: TA with more weight in module's ROL
     */
    function compareHighestTaRankForModule(string $module_id, string $ta1_email, string $ta2_email)
    {
        // which ta has higher weight in module rol
        // return ta_email of ta with highest weight in module ROL

    }
}
