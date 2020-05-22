<?php

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
                'ta_id' =>$ta->email,
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

        // create an array of removed TAs
        $removed_tas = [];

        // allocate the removed TAs if exist
        if(count($removed_tas) > 0)
        {
            foreach($removed_tas as $removed)
            {
                $allocations_matrix = $this->allocate($removed, $allocations_matrix);
            }
        }


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
                            $ta_weight_for_module = $allocation_class->getTaWeightForModuleForCurrentSemester($current_ta_id, $module_choice_id);

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
                                    // print_r($allocations_matrix['module_allocations'][$module_choice_id]['tas']);
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
                                    $crnt_ta_weight_for_crnt_module = $modules_prefs_and_ROLs[$module_choice_id]['tas'][$current_ta_id]['weight'];

                                    // Allocate new TA in the cleared key: $key_to_remove
                                    $allocations_matrix['module_allocations'][$module_choice_id]['tas'][$key_to_remove] = ['weight' => $crnt_ta_weight_for_crnt_module, 'ta_id' => $current_ta_id];



                                    // Allocate current module to current TA
                                    $allocations_matrix['ta_allocations'][$current_ta_id]['modules'][] = $module_choice_id;

                                    // Add module's working hours to TA's weekly_working_hours
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['weekly_working_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours'];

                                // Add removed ta to removed tas array
                                $removed_tas[] = $all_tas_prefs_and_Rols[$ta_id_to_remove];
                                 // Pass removed TA to allocate function
                                //  $removed_ta = $all_tas_prefs_and_Rols[$ta_id_to_remove];

                                //  $allocations_matrix = $this->allocate($removed_ta, $allocations_matrix);


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


    /*
                // creating a new instance of Module_preference to save to DB
                $module_pref = new module_preference();

                // Adding the posted attributes to the created instance
                $module_pref->module_id = $request->input('module_id');
                $module_pref->no_of_assistants = abs($request->input('no_of_assistants'));
                $module_pref->no_of_contact_hours = abs(ceil($request->input('no_of_contact_hours')));
                $module_pref->no_of_marking_hours = abs(ceil($request->input('no_of_marking_hours')));
                $module_pref->academic_year = $request->input('academic_year');
                // $module_pref->semester = $request->input('semester');


                ////////////
                // Creating an array to save ta_module_choice instances
                $used_languages_array = [];
                // Counting the lenght of the array
                $arrayLength = count($used_languages_array);

                //for loop: Check module choices and save
                for($i=1; $i<=7; $i++)
                {
                    // Creating an instance of the ta_module_choices
                    $used_language_choice = new used_langauge();

                    // Will loop and get IDs of submitted choices
                    $used_languages_choice_id =  $request->input('language_'.$i.'_id');

                    if($used_languages_choice_id != NULL) // if ID is not null
                    {
                        $used_language_choice->module_id = $request->input('module_id');
                        $used_language_choice->language_id = $used_languages_choice_id;
                        $used_language_choice->academic_year = $current_academic_year->year;

                        // Checking the length of the array to calculate the array key at which the instance will be saved
                        // Counting the lenght of the array
                        $arrayLength = count($used_languages_array);


                        if($arrayLength == 0) // If array is now empty
                        {
                            // Add to array WITHOUT chaning the key
                            $used_languages_array[$arrayLength] = $used_language_choice;
                        }
                        else // if array is not empty
                        {
                            // Add WITH changing the key
                            $used_languages_array[$arrayLength] = $used_language_choice;
                        }
                    }
                    else // once we get a null used_languages_array_id
                    {
                        // Do nothing
                    }
                }

                // $arrayLength = count($used_languages_array);

                // All saving will be in the try catch

                try
                {
                    // remove old values
                    used_langauge::where('academic_year', '=', $request->input('academic_year'))
                                        ->where('module_id', '=', $request->input('module_id'))
                                        ->delete();

                    module_preference::where('module_id', '=', $request->input('module_id'))
                                        ->where('academic_year', '=', $request->input('academic_year'))
                                        ->delete();

                    $used_language_to_save = new used_langauge();
                    // Save to module_preferences table
                    $module_pref->save();

                    // If languages have been chosen
                    if(sizeof($used_languages_array) > 0)
                    {
                        // Save to ta_module_choices table
                        for($j = 0; $j <= $arrayLength; $j++)
                        {
                            $used_language_to_save = $used_languages_array[$j];
                            $used_language_to_save->priority = $j+1;
                            $used_language_to_save->save();
                        }
                    }
                }
                catch (QueryException $e)
                {
                    return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: ' . $e);
                }
            */
