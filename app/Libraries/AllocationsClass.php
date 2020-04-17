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
    function createFinalRolsForModulesForYear(string $academic_year)
    {
        $basic_db_calss = new BasicDBClass();

        // Create the matrix of the modules' ROLs
        // tast matrix
        $modules_ROLs_test['test'] =
        [
            'no_of_assistants' => 4,
            'tas' =>
            [
                ['weight' => 35, 'ta_email' => 'gta1'],
                ['weight' => 18, 'ta_email' => 'ta1'],
                ['weight' => $smart=23, 'ta_email' => 'gta2'],
            ]
        ];

        // Actual matrix
        $modules_ROLs = [];

        // Get all Modules With prefs for year
        $all_modules_with_prefs = $basic_db_calss->getModulesWithPrefsForYear($academic_year);

        foreach($all_modules_with_prefs as $module)
        {
            // Get top TAs from primary ROL based on no_of_assistants: e.g. top 3 TAs if no_of_assistants = 3
            $top_tas = DB::table('module_rank_order_lists')->select('ta_email', 'ta_total_weight')->where('module_id','=',$module->module_id)->orderBy('ta_total_weight', 'DESC')->take($module->no_of_assistants)->get();

            // Declare a clear TAs array: $tas
            $tas = [];

            // Iterate through the top TAs
            foreach($top_tas as $ta)
            {

                // Add to Array of TAs for module
                $current_ta =
                [
                    'weight' => $ta->ta_total_weight,
                    'ta_email' => $ta->ta_email
                ];

                $tas[] = $current_ta;
            }

            // assign module id to the entry in the matrix
            $modules_ROLs[$module->module_id] =
            [
                'no_of_assistants' => $module->no_of_assistants,
                'tas'=> $tas
            ];

            // $modules_ROLs[$module->module_id]->no_of_assistants = $module->no_of_assistants;

            // $modules_ROLs[$module->module_id]->tas = $tas;
        }

        // TEST
        // $modules_ROLs['test'] =
        // [
        //     'no_of_assistants' => 4,
        //     'tas' =>
        //     [
        //         ['weight' => 35, 'ta_email' => 'gta1'],
        //         ['weight' => 18, 'ta_email' => 'ta1'],
        //         ['weight' => $smart=23, 'ta_email' => 'gta2'],
        //     ]
        // ];
        return $modules_ROLs;
    }

    function createFinalRolsForTas()
    {
        // Gat all TAs with prefs
    }

    function getTopTasForModule(int $no_of_assistants)
    {
        DB::table('module_rank_order_list')->orderBy('priority', 'DESC')->get($no_of_assistants);
    }

    function getAllTaRols()
    {
        //
    }

    function getAllModuleRols()
    {
        //
    }

    function getOneModuleRol(string $module_id)
    {
        //
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
