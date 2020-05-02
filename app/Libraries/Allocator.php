<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;

class Allocator
{

    public function allocate(array $ta, $allocations_matrix)
    {
        $basic_DB_access = new BasicDBClass();
        $allocation_class = new AllocationsClass();
        $academic_year = $basic_DB_access->getCurrentAcademicYear();

        $current_ta_id = $ta['ta_id'];

        // Get all TAs and their prefs and ROLS
        $all_tas_prefs_and_Rols =  $allocation_class->createTasRolsAndPrefsForYear($academic_year);
        // Get all Modules with their prefs and ROLS
        $modules_prefs_and_ROLs =  $allocation_class->createFinalRolsForModulesForYear($academic_year);


        /** TODO:
         *
         *  Check if TA is not already assigned to Module
         *
         */
         // Check if max working hours is reached, OR ta can taif so move to next TA
        if(($allocations_matrix['ta_allocations'][$current_ta_id]['weekly_working_hours'] >= $ta['max_weekly_working_hours'])
            || ($ta['max_modules'] < (sizeof($allocations_matrix['ta_allocations'][$current_ta_id]['modules']) + 1)))
        {
            // Do notihing, skip this TA and go to next one
            echo 'TA has reached max weekly working hours OR max number of modules';
        }
        else
        {
            // echo 'max working hours not reached';

            // Iterate through module choices
            for($i = 1; $i <= count($ta['modules']); $i++)
            {
                // Get module choice ID, $i represents the priority of the module choice in the TA's ROL
                $module_choice_id = $ta['modules'][$i]['module_id'];


                // Check if adding current_module's working hours to TA's working hours will exceed max, OR adding another module will exceed TA's max number of modules
                if(($allocations_matrix['ta_allocations'][$current_ta_id]['weekly_working_hours'] + $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours']) > $ta['max_weekly_working_hours']
                    || $ta['max_modules'] < (count($allocations_matrix['ta_allocations'][$current_ta_id]['modules']) + 1))
                {
                    // Skip this moduel and go to next
                    // echo 'Adding this module will lead to exceeding TA\'s max weekly working hours' . $current_ta_id . $modules_prefs_and_ROLs[$module_choice_id];
                }
                else
                {
                    // Check if module has submitted prefs
                    if(array_key_exists($module_choice_id, $modules_prefs_and_ROLs))
                    {
                        // Check if there are unallocated positions
                        // if number of allocated TAs is less than no_of_assistants required
                        if ((sizeof($allocations_matrix['module_allocations'][$module_choice_id]['tas'])) < $modules_prefs_and_ROLs[$module_choice_id]['no_of_assistants'])
                        {
                            //Get current TA's weight for current module from DB
                            $ta_weight_for_module = $allocation_class->getTaWeightForModule($current_ta_id, $module_choice_id);

                            // Allocate current TA to current module
                            $allocations_matrix['module_allocations'][$module_choice_id]['tas'][] = ['ta_id' => $current_ta_id, 'weight' => $ta_weight_for_module];

                            // Allocate current module to current TA
                            $allocations_matrix['ta_allocations'][$current_ta_id]['modules'][] = $module_choice_id;

                            // Add module's working hours to TA's weekly_working_hours
                            $allocations_matrix['ta_allocations'][$current_ta_id]['weekly_working_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours'];
                        }
                        else
                        {
                            // Check if TA exists in the module's ROL: True if it exists, False if not
                            if(array_key_exists($current_ta_id, $modules_prefs_and_ROLs[$module_choice_id]['tas']))
                            {
                                /**
                                 *      Remove the TA with the least weight from the allocation
                                 */
                                    // Get the min value
                                    $min_weight = min(array_column($allocations_matrix['module_allocations'][$module_choice_id]['tas'], 'weight'));

                                    // array_column creates a new array with data from the chosen column with keys from 0
                                    // array-search returns the key of the searched item from the new array created by array_column
                                    $key_to_remove = array_search($min_weight, array_column($allocations_matrix['module_allocations'][$module_choice_id]['tas'], 'weight'));

                                    // save the email of the removed TA with the min
                                    $ta_id_to_remove = $allocations_matrix['module_allocations'][$module_choice_id]['tas'][$key_to_remove]['ta_id'];

                                    // Remove TA with min weight from module's allocations
                                    unset($allocations_matrix['module_allocations'][$module_choice_id]['tas'][$key_to_remove]);

                                /**
                                 *      Remove target module from removed TA's allocations
                                 */

                                    //Get the module's key in the removed TA's allocation array
                                    $module_key_to_remove = array_search($module_choice_id, $allocations_matrix['ta_allocations'][$ta_id_to_remove]['modules']);

                                    // Remove module from removed TA's allocations
                                    unset($allocations_matrix['ta_allocations'][$ta_id_to_remove]['modules'][$module_key_to_remove]);

                                    // Remove module's working hours from TA's weekly_working_hours
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['weekly_working_hours'] -= $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours'];

                                /**
                                 *      Allocate current TA and module to each other
                                 */

                                    // Allocate current TA to current module

                                    // EDIT TO ADD WEIGHT AS WELL
                                    $crnt_ta_weight_for_crnt_module = $modules_prefs_and_ROLs[$module_choice_id][$current_ta_id]['weight'];

                                    $allocations_matrix['module_allocations'][$module_choice_id]['tas'][] = ['weight' => $crnt_ta_weight_for_crnt_module, 'ta_id' => $current_ta_id];



                                    // Allocate current module to current TA
                                    $allocations_matrix['ta_allocations'][$current_ta_id]['modules'][] = $module_choice_id;

                                    // Add module's working hours to TA's weekly_working_hours
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['weekly_working_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours'];

                                 // Pass removed TA to allocate function
                                 $removed_ta = $all_tas_prefs_and_Rols[$ta_id_to_remove];

                                 $allocations_matrix = $this->allocate($removed_ta, $allocations_matrix);


                            }
                        }
                    }
                    else
                    {
                        return back()->with('alert', 'has not submitted preferences for current academic year!');
                    }
                }
            }
        }

        return $allocations_matrix;
    }






}
