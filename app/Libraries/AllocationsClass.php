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
    function intitiateTaAllocationMatrix()
    {
        $basic_db_calss = new BasicDBClass();

        $ta_allocation_matrix =[];

        // Get all Modules With prefs for year
        $all_active_tas = $basic_db_calss->getAllActiveTas();

        foreach($all_active_tas as $ta)
        {
            // An empty array of TAs
            $modules = array();

            $ta_allocation_matrix[$ta->email] =
            [
                'weekly_working_hours' => 0,
                'modules' => $modules
            ];
        }

        return $ta_allocation_matrix;
    }

    /**
     * @return array
     */
    function initiateAllocationsMatrix(string $academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        // Get all Modules With prefs for year
        $all_modules = $basic_db_calss->getAllModulesForYear($academic_year);

        $allocations_matrix = [];

        $ta_allocations =[];
        $module_allocations =[];

        // Get all Modules With prefs for year
        $all_active_tas = $basic_db_calss->getAllActiveTas();

        foreach($all_active_tas as $ta)
        {
            // An empty array of TAs
            $modules = [];

            $ta_allocations[$ta->email] =
            [
                'weekly_working_hours' => 0,
                'modules' => $modules
            ];
        }

        foreach($all_modules as $module)
        {
            // An empty array of TAs
            $tas = array();

            $module_allocations[$module->module_id] =
            [
                'tas' => $tas
            ];
        }

        $allocations_matrix['ta_allocations'] = $ta_allocations;
        $allocations_matrix ['module_allocations']= $module_allocations;

        return $allocations_matrix;
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
            $top_tas = DB::table('module_rank_order_lists')
                            ->select('ta_email', 'ta_total_weight')
                            ->where('module_id','=',$module->module_id)
                            ->orderBy('ta_total_weight', 'DESC')
                            // ->take($module->no_of_assistants)
                            ->get();

            // Declare a clear TAs array: $tas
            $tas = [];

            // initiate priority of TAs
            $ta_priority = 1;

            // Iterate through the top TAs
            foreach($top_tas as $ta)
            {
                $ta_id = $ta->ta_email;

                // Add to Array of TAs for module
                $current_ta =
                [
                    'weight' => $ta->ta_total_weight,
                    'ta_id' => $ta_id,
                    'ta_priority' => $ta_priority
                ];

                $tas[$ta_id] = $current_ta;

                // Add to priority
                $ta_priority++;
            }


            // assign module id to the entry in the matrix
            $modules_ROLs[$module->module_id] =
            [
                'no_of_assistants' => $module->no_of_assistants,
                'weekly_working_hours' => $module->no_of_contact_hours + $module->no_of_marking_hours,
                'tas'=> $tas
            ];
        }

        return $modules_ROLs;

        /**  // TEST ROL
         *
         * $current_ta =
         *        [
         *            'weight' => 100,
         *            'ta_email' => 'lol'
         *        ];
         *        $tas['haha'] = $current_ta;
         *
         *
         * $modules_ROLs['test'] =
         * [
         *    'no_of_assistants' => 4,
         *    'tas' => $tas
         * ];
         *
         * $ta_exists_in_Rol = (array_key_exists("some_ta_email",$modules_ROLs['test']['tas'])) ? true : false;
         * return $ta_exists_in_Rol;
         * // End of test
         */

    }

    /**
     * Returns a matrix of TAs with their preferences and arrays of all their preferred modules and their priorities.
     *
     * @param string $academic_year
     *
     * @return array key[ta_id]: {int weekly_working_hours, int max_weekly_working_hours, int max_modules, array modules: [{ key module_id: value priority}]}
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
            $ta_id = $ta->ta_email;

            // Get the modules a TA has chosen and their priorities
            $preferred_modules_for_current_ta = DB::table('ta_module_choices')
                                                ->select('module_id', 'priority')
                                                ->where('ta_email','=',$ta_id)
                                                ->where('preference_id','=',$ta->preference_id)
                                                ->orderBy('priority', 'ASC')
                                                ->get();

            // Get the modules that TA has not chosen
            $not_preferred_modules_for_ta = DB::table('modules')
                                                ->select('module_id')
                                                ->where('academic_year','=',$academic_year)
                                                ->whereNotExists(function($query)use($academic_year, $ta)
                                                {
                                                    $query->select(DB::raw(1))
                                                        ->from('ta_module_choices')
                                                        // ->whereRaw('preference_id','=',$ta->preference_id);
                                                        ->where('preference_id','=',$ta->preference_id);
                                                    })
                                                ->get();

            // Calculate max_working_hours
            $max_weekly_working_hours = $ta->max_contact_hours + $ta->max_marking_hours;
            // If none is submitted, calculate based on having Tier 4 visa or not.
            if($max_weekly_working_hours == 0)
            {
                ($ta->have_tier4_visa == true) ? $max_weekly_working_hours = 20 :  $max_weekly_working_hours = 40;
            }
            ($ta->have_tier4_visa == true) ? $max_weekly_working_hours = 20 :  $max_weekly_working_hours = 40;

            // Declare an empty modules array
            $modules = [];

            // Iterate through the modules
            foreach($preferred_modules_for_current_ta as $module)
            {
                // Add to Array of modules for current TA
                $modules[$module->priority] =
                [
                    'module_id' => $module->module_id
                ];
            }

            // Add the not preferred modules to the module array regardless of the priority
            foreach($not_preferred_modules_for_ta as $module)
            {
               // Add to Array of modules for current TA
               $modules[] =
               [
                   'module_id' => $module->module_id
               ];
            }

            // Assign TA id to the entry in the matrix
            $all_tas_prefs_and_ROLs[$ta_id] =
            [
                'ta_id' => $ta_id,
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
     * @param string $ta1_email: first ta_id
     * @param string $ta2_email: second ta_id
     *
     * @return string $ta_id: TA with more weight in module's ROL
     */
    function compareHighestTaRankForModule(string $module_id, string $ta1_id, string $ta2_id)
    {
        // which ta has higher weight in module rol
        // return ta_id of ta with highest weight in module ROL

    }

    function getTaWeightForModule(string $ta_id, string $module_id)
    {
        // Access module_rank_order_lists to get the weight of $ta_id against $module_id in $current_academic_year
        $basic_db_calss = new BasicDBClass();

        $current_academic_year = $basic_db_calss->getCurrentAcademicYear();

        return DB::table('module_rank_order_lists')
                        ->where('academic_year','=', $current_academic_year)
                        ->where('ta_email','=',$ta_id)
                        ->where('module_id','=',$module_id)
                        ->first()->ta_total_weight;
    }

    /**
     * Checks if allocation exists for year
     *
     * @param $academicYear
     *
     * @return boolean
     *
     * True if allocation exists
     * Flase if noe
     */
    function allocationExistsForYear($academic_year)
    {
        return DB::table('allocations')->where('academic_year', '=', $academic_year)->exists();
    }
}
