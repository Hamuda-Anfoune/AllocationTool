<?php

namespace App\Http\Controllers\Allocation;

use App\Http\Controllers\Controller;
use App\Libraries\AllocationsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ModuleRankOrderList;
use App\Libraries\BasicDBClass;
use App\Libraries\WeightsClass;
use App\Libraries\Allocator;
use Illuminate\Database\QueryException;

class AllocationController extends Controller
{
    /* TODO:
     *  Hange how did_before is calculated to counting how many previous allocations where a TA is allocated to a module
     */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* TODO - INDEX:
          *  Get number of Modules
          *  Get number of active convenors
          *  Get number of active TAs
          *  Get number of all active users
          *  Get data about module prefs
          *  Get data about TA prefs
          *  Get data about allocations if any
          *
          *  Filter and organise this data and send to view
          */

        return view('allocations.index');



    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(request $req)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
         * ALLOCATION ALGORITHM STARTS HERE
         *
         *
         * TODO:
         *
         * Assign remaining openings to TA's with remaining hours
        */

        $basic_DB_access = new BasicDBClass();
        $allocation_class = new AllocationsClass();

        $academic_year = $basic_DB_access->getCurrentAcademicYear();

        /** TODO: uncomment to reactivate
         *
         *      CHECK IF ALL ACTIVE TAs AND MODULES HAVE SUBMITTED PREFS
        */

        // Get all modules without submitted preferences
        $modules_without_prefs = $basic_DB_access->getModulesWithoutPrefsForYear($academic_year);

        // Get all TAs without submitted preferences
        $tas_without_prefs = $basic_DB_access->getActiveTasWithoutPrefsForYear($academic_year);


        $allocator = new Allocator();
        // return $allocator->allocate($tas_without_prefs);

        // Redirect to show missing prefs in case there are any!
        if(count($tas_without_prefs) > 0 || count($modules_without_prefs) > 0)
        {
            return redirect('/allocations/missing-prefs');
        }

        /**
         *      CREATE AND GET ALL ROLs AND PREFS FOR THIS YEAR
         */

        // Get all TAs and their prefs and ROLS
        $all_tas_prefs_and_Rols =  $allocation_class->createTasRolsAndPrefsForYear($academic_year);

        // Get all Modules with their prefs and ROLS
        $modules_prefs_and_ROLs =  $allocation_class->createFinalRolsForModulesForYear($academic_year);

        // Intitiate allocation matrix
        $allocations_matrix = $allocation_class->initiateAllocationsMatrix($academic_year);
        /**
         *      ITEREATE THROUGH THE TAs TO ALLOCATE
         */

        foreach($all_tas_prefs_and_Rols as $ta)
        {
            $allocations_matrix = $this->allocate($ta, $allocations_matrix);
        }

        // Once all is done check if there are modules or TAs without allocation

        // create a way to allocate those

        // Save to DB

        print_r($allocations_matrix);
        return $allocations_matrix['ta_allocations'];
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


         // Check if max working hours is reached, if so move to next TA
        if(($allocations_matrix['ta_allocations'][$current_ta_id]['weekly_working_hours'] >= $ta['max_weekly_working_hours'])
            || ($ta['max_modules'] < (sizeof($allocations_matrix['ta_allocations'][$current_ta_id]['modules']) + 1)))
        {
            // Do notihing, skip this TA and go to next one
            echo 'TA has reached max weekly working hours';
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        echo 'Next time you come here, we\'ll delete your aloocation for the current semester!';
    }


    /**
     * This will create primary module ROLs and save them to the database.
     * These lists are primary because they have all TAs with weights for all modules,
     * and they are NOT oredered based on weight.
     */
    public function createModuleROLs() //ROL: Rank Order List: list of TAs in order of preference
    {
        $basic_DB_access = new BasicDBClass();
        $weights_class = new WeightsClass();


        /*
         * START OF FOREIGN CODE
         */

        /* TODO:
         *  Calculate Weight of having the module as choice for TA
         *      - did_before
         *      - weight of priority of module in ROL
         *
         *  Calculate weight of having programming languages in common with module
         */


        // Get current academic year
        $current_academic_year = $basic_DB_access->getCurrentAcademicYear();

        // Check if ROLs already created for current semester
        if(count(DB::table('module_rank_order_lists')->where('academic_year','=', $current_academic_year)->get()) > 0)
        {
            return redirect('/allocations/store');

            // COMMENT the below out and UNCOMMENT the one above
            // return redirect('/allocations')->with('alert', 'Modules Rank Order List Already Created for this semester!');
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
                        /* TODO:
                         *
                         *  Find a way to calculate how many times current ta has assisted with current module before
                        */

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
                $ta_language_choices = $basic_DB_access->getTaLanguageChoicesForYear($ta->preference_id)->toArray();

                foreach($module_used_languages as $language)
                {
                    // compare language with TA_language
                    foreach($ta_language_choices as $ta_language_choice)
                    {
                        if($language->language_id === $ta_language_choice->language_id) // Certified Working!!
                        {
                            // echo 'WE HAve a match ppl!';
                            // echo 'target: '. $language->language_id . 'goal: '. $ta_language_choice->language_id;

                            //Get the language pririty to calculate its weight which willbe added to the TA_total_weight
                            $language_priority_weight =  $weights_class->getWeightForOneLanguagePriority($language->priority);

                            // Add weight ot total weight
                            $ta_rank_details->ta_total_weight += $language_priority_weight;
                            // Add weight to weight for similar language choices between TA & module
                            $ta_rank_details->languages_similarity_weight += $language_priority_weight;
                        }

                    }

                    /*
                     * --------------------------
                     *
                     *        SAVE TO DB
                     *
                     * --------------------------
                    */
                    try
                    {
                        $ta_rank_details->save();
                    }
                    catch(QueryException $qe)
                    {
                        DB::table('module_rank_order_lists')->where('academic_year','=', $current_academic_year)->delete();
                        return redirect('/allocations')->with('alert', 'Something went wrong: ' . $qe);
                    }



                }

            } // End of foreach TA

        } // end of foreach module

        return redirect('/allocations/store');

        // TODO: comment this out and uncomment the one above
        // return redirect('/allocations')->with('success', 'Modules Rank Order List Created');
    }

    public function deleteModuleROLs()
    {
        $basic_DB_class = new BasicDBClass();
        $academic_year = $basic_DB_class->getCurrentAcademicYear();
        DB::table('module_rank_order_lists')->where('academic_year','=', $academic_year)->delete();

        return back()->with('success', 'Module Rank Order Lists have been dileted for current academic year: '. $academic_year);
    }


    public function missingPrefs()
    {
        $title = 'Missing Module and TA Preferences';

        $basic_DB_access = new BasicDBClass();

        $academic_semester = $basic_DB_access->getCurrentAcademicYear();

        // Get all modules without submitted preferences
        $modules_without_prefs = $basic_DB_access->getModulesWithoutPrefsForYear($academic_semester);

        // Get all TAs without submitted preferences
        $tas_without_prefs = $basic_DB_access->getActiveTasWithoutPrefsForYear($academic_semester);

        if(count($tas_without_prefs) > 0 || count($modules_without_prefs) > 0)
        {
            return view('allocations/missingPrefs')
                        ->with('alert', 'Teaching Assistants have not submitted preferences for current academic semester!')
                        ->with('modules_without_prefs', $modules_without_prefs)
                        ->with('tas_without_prefs', $tas_without_prefs)
                        ->with('title', $title);
        }

        return view('allocations/missingPrefs')->with('title', $title);
    }
}
