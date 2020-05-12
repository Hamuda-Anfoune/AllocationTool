<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;
use App\ModuleRankOrderList;
use Illuminate\Database\QueryException;

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
        if(($allocations_matrix['ta_allocations'][$current_ta_id]['contact_hours'] >= $ta['max_contact_hours'])
            || ($allocations_matrix['ta_allocations'][$current_ta_id]['marking_hours'] >= $ta['max_marking_hours'])
            || ($ta['max_modules'] < (sizeof($allocations_matrix['ta_allocations'][$current_ta_id]['modules']) + 1)))
        {
            // Do notihing, skip this TA and go to next one
            // echo 'TA has reached max weekly working hours OR max number of modules';
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
                if(($allocations_matrix['ta_allocations'][$current_ta_id]['contact_hours'] + $modules_prefs_and_ROLs[$module_choice_id]['contact_hours']) > $ta['max_contact_hours']
                    || ($allocations_matrix['ta_allocations'][$current_ta_id]['marking_hours'] + $modules_prefs_and_ROLs[$module_choice_id]['marking_hours']) > $ta['max_marking_hours']
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
                            $allocations_matrix['ta_allocations'][$current_ta_id]['contact_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['contact_hours'];
                            $allocations_matrix['ta_allocations'][$current_ta_id]['marking_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['marking_hours'];
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
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['contact_hours'] -= $modules_prefs_and_ROLs[$module_choice_id]['contact_hours'];
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['marking_hours'] -= $modules_prefs_and_ROLs[$module_choice_id]['marking_hours'];

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
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['contact_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['contact_hours'];
                                    $allocations_matrix['ta_allocations'][$ta['ta_id']]['marking_hours'] += $modules_prefs_and_ROLs[$module_choice_id]['marking_hours'];

                                // Add removed ta to removed tas array
                                $removed_tas[] = $all_tas_prefs_and_Rols[$ta_id_to_remove];
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


    public function createModuleROLs() //ROL: Rank Order List: list of TAs in order of preference
    {
        $basic_DB_access = new BasicDBClass();
        $weights_class = new WeightsClass();

        // Get current academic year
        $current_academic_year = $basic_DB_access->getCurrentAcademicYear();

        /* TODO:
         *  consider similarity in numbers of working hours  in calculation
         */

        // Check if ROLs already created for current semester
        if(DB::table('module_rank_order_lists')->where('academic_year','=', $current_academic_year)->exists() > 0)
        {
            DB::table('module_rank_order_lists')->where('academic_year','=', $current_academic_year)->delete();
        }


        // list of modules with preferences
        $modules_with_prefs = $basic_DB_access->getModulesWithPrefsForYear($current_academic_year);

        // list of TAs with submitted prefs in the current academic year
        // With preference IDs
        $TAs_wtih_prefs = $basic_DB_access->getTAsWithPrefsForYear($current_academic_year);

        foreach($modules_with_prefs as $module)
        {
            // Get used languages for this module
            $module_used_languages = $basic_DB_access->getUsedLanguagesForModuleForYear($module->module_id, $current_academic_year);

            /*
            * Handling TAs HERE
            */

            foreach($TAs_wtih_prefs as $ta)
            {
                $ta_id = $ta->ta_email;
                // Create an object of the module_ROL to store the TAs details
                $ta_rank_details = new ModuleRankOrderList();

                // Set basic attributes to the object: ModuleRankOrderList()
                $ta_rank_details->module_id = $module->module_id;
                $ta_rank_details->academic_year = $current_academic_year;
                $ta_rank_details->ta_email = $ta_id;

                // Get data where module exists in TA's module choices for current year, preferences differ based on Academic year
                $current_ta_with_current_module = DB::table('ta_module_choices')
                                                    ->select('priority', 'did_before')
                                                    ->where('preference_id', '=', $ta->preference_id)
                                                    ->where('module_id', '=', $module->module_id)
                                                    ->first();

                /*
                 *  CALCULATE WEIGHTS FOR:
                 *      ASSISSTING WITH MODULE BEFORE
                 *      PRIORITY OF MODULE IN THE RANK ORDER LIST (ROL)
                 */
                // If module is a preference for TA
                if($current_ta_with_current_module != null)
                {
                    // calculate weight to give if done before
                    if($current_ta_with_current_module->did_before == true)
                    {

                        $did_before_weight = $weights_class->calculateRepetitionWeightForModuleForTa($ta_id, $module->module_id);

                        // WEIGHT FOR ASSISSTING WITH MODULE BEFORE
                        // Register weight of assissting with this module before
                        $ta_rank_details->did_before_weight = $did_before_weight;
                        // Add weight of assissting before to total weight of TA for current module
                        $ta_rank_details->ta_total_weight += $did_before_weight;
                    }

                    // WEIGHT FOR PRIORITY OF MODULE IN THE RANK ORDER LIST (ROL)
                    // calculate weight to give to TA based on module priority
                    $ta_rank_details->module_priority_for_ta = $current_ta_with_current_module->priority;
                    $ta_rank_details->module_priority_for_ta_weight = $weights_class->getWeightForModulePriority($current_ta_with_current_module->priority);
                    $ta_rank_details->ta_total_weight += $weights_class->getWeightForModulePriority($current_ta_with_current_module->priority);
                }

                // WEIGHT FOR HAVING PROGRAMMING LANGUAGES IN COMMON
                // Get a language choices for TA
                $ta_language_choices = $basic_DB_access->getTaLanguageChoicesForPreference($ta->preference_id)->toArray();

                foreach($module_used_languages as $language)
                {
                    // compare language with TA_language
                    foreach($ta_language_choices as $ta_language_choice)
                    {
                        if($language->language_id === $ta_language_choice->language_id) // Certified Working!!
                        {
                            //Get the language pririty to calculate its weight which willbe added to the TA_total_weight
                            $language_priority_weight =  $weights_class->getWeightForOneLanguagePriority($language->priority);

                            // Add weight ot total weight
                            $ta_rank_details->ta_total_weight += $language_priority_weight;
                            // Add weight to weight for similar language choices between TA & module
                            $ta_rank_details->languages_similarity_weight += $language_priority_weight;
                        }
                    }
                }
                try
                {
                    $ta_rank_details->save();
                }
                catch(QueryException $qe)
                {
                    DB::table('module_rank_order_lists')->where('academic_year','=', $current_academic_year)->delete();
                    return $qe;
                }

            } // End of foreach TA

        } // end of foreach module

        return true;
    }
}
