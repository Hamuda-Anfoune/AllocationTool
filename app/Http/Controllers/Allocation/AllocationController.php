<?php

namespace App\Http\Controllers\Allocation;

use App\Http\Controllers\Controller;
use App\Libraries\AllocationsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ModuleRankOrderList;
use App\Libraries\BasicDBClass;
use App\Libraries\WeightsClass;

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
        /*
         * -----------------------------
         *
         *    START OF ORIGINAL CODE
         *
         * -----------------------------
         */

        /* TODO - INDEX:
          *  Get number of Modules
          *  Get number of convenors
          *  Get number of TAs
          *  Get data about module prefs
          *  Get data about TA prefs
          *  Get data about allocations if any
          *
          *  Filter and organise this data and send to view
          */

        //return view('allocations.index');

        /*
         * -----------------------------
         *
         *     END OF ORIGINAL CODE
         *
         * -----------------------------
         */


        /*
         * ALLOCATION ALGORITHM STARTS HERE
         *
         * TODO:
         *  Get TAs' ROLs
         *  Create final modules' ROLs: with exact order based on weight
        */

        // create a matrix of modules_ROLs
        // add only the number of assistants the module wants
        // when comparing
        $basic_DB_access = new BasicDBClass();
        $allocation_class = new AllocationsClass();

        $academic_year = $basic_DB_access->getCurrentAcademicYear();



        // Get all TAs and their prefs and ROLS
        $all_tas_prefs_and_Rols =  $allocation_class->createTasRolsAndPrefsForYear($academic_year);
        // Get all Modules with their prefs and ROLS
        $modules_prefs_and_ROLs =  $allocation_class->createFinalRolsForModulesForYear($academic_year);

        // Intitiate allocation matrices for TAs
        $ta_allocation_matrix = [];

        // Intitiate allocation matrices for nodules
        $module_allocation_matrix = $allocation_class->intitiateModuleAllocationMatrix($academic_year);

        // array_search below returns the index not the priority,
        // We are using the priority as keys
        // As indeces start from 0 and priority starts from 1:
        // we will adding 1 to the return of this function
        $piority_test = 1 + array_search('CO7105', array_column($all_tas_prefs_and_Rols['gta1@gmail.com']['modules'], 'module_id'));

        foreach($all_tas_prefs_and_Rols as $ta)
        {
            /**
             * TODO:
             *   Check if allcoation is done
             */
            // Iterate through module choices
            for($i = 1; $i <= count($ta['modules']); $i++)
            {
                // Get module choice ID, $i represents the priority of the module choice in the TA's ROL
                $module_choice_id = $ta['modules'][$i]['module_id'];
                $current_ta_email = $ta['ta_email'];
                // module_priority = $i, AS PRIORITY IS THE KEY REPRESENTED IN $i ABOVE,

                // print_r($module_allocation_matrix);
                // echo 'haha';


                //$min_weight = min(array_column($modules_prefs_and_ROLs[$module_choice_id]['tas'], 'weight'));

                //echo $module_choice_id;
                // echo 'min: ' . $min_weight;


                // $keyy = 1 + array_search($min_weight, array_column($modules_prefs_and_ROLs[$module_choice_id]['tas'], 'weight'));
                // echo $keyy;
                // echo $modules_prefs_and_ROLs[$module_choice_id]['tas'][$keyy]['ta_email'];
                // print_r($modules_prefs_and_ROLs[$module_choice_id]['tas'][$keyy]);



                // Check if module has submitted prefs
                if(array_key_exists($module_choice_id, $modules_prefs_and_ROLs))
                {
                    // Check if there are unallocated positions
                    // if number of allocated TAs is less than no_of_assistants required
                    if ((sizeof($module_allocation_matrix[$module_choice_id]['tas'])) < $modules_prefs_and_ROLs[$module_choice_id]['no_of_assistants'])
                    {
                        // allocate to module
                    }
                    else
                    {
                        // Check if TA exists in the module's ROL: True if it exists, False if not
                        if(array_key_exists($current_ta_email, $modules_prefs_and_ROLs[$module_choice_id]['tas']))
                        {
                            // Remove the TA with the least weight from the allocation,
                            // Get the min value
                            $min_weight = min(array_column($module_allocation_matrix[$module_choice_id]['tas'], 'weight'));

                            // Get the array key if the min weight, key = index + 1,
                            // because we are using the priority as keys, index starts at 0, but priority starts at 1
                            $key_to_remove = 1 + array_search($min_weight, array_column($modules_prefs_and_ROLs[$module_choice_id]['tas'], 'weight'));

                            // save the email of the TA with the min
                            $ta_email_to_remove = $modules_prefs_and_ROLs[$module_choice_id]['tas'][$key_to_remove]['ta_email'];

                            /**
                             * TODO:
                             *   allocate current TA and module to each other
                             *   call allocation function and pass removed TA
                             */
                        }






                        // Check if current TA has got more weight than any of the allocated ones
                        // Get current TA's weight
                        $current_ta_weight =  $modules_prefs_and_ROLs[$module_choice_id]['tas'][$current_ta_email]['weight'];
                        // Fiter allocated TAs with more weight than the current TA's weight
                        $fitered = array_filter(
                            $module_allocation_matrix[$module_choice_id]['tas'],
                            function ($key) use($current_ta_weight) {
                                return ($key > $current_ta_weight);
                            }, ARRAY_FILTER_USE_KEY
                        );

                        echo $current_ta_weight;

                    }

                }
                else
                {
                    echo $module_choice_id . 'has not submitted preferences for current academic year!';
                }






            }
        }




        /**
         * END OF ALLOCATION
         */

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules_with_prefs = DB::table('module_preferences')->select('module_id')->get();

        return $modules_with_prefs;



        /* Module preferences
        - Has TA helped with module before
        - How similar are the language preferences
    */

    /* Module Constraints:
        - number of needed TAs
        - //
    */


    /* TA Constraints:
        - number of work hours
        - //
    */


    // TAs and Modules are agents

    // Get a list of TAs: T = {t1, ..., tn}

    // each TA has got a Rank Order List (ROL): an ordered list of preferred modules: Preferred Module List PML =

    // get a list of modules with preferences: M = {m1, ..., mn} mn: m of n

    // For each module m in M, there is an integer number pm,which represents the number of positions m can take: int qm

    /* Also, for each m in M, there is an integer number chm, which represnets the number of contach hours  required by m,
     * and an integer mhm representing the marking hours required by m:
     *      chm=> contact hours required by m
     *      mhm=> marking hours required by m
     */

    /* Each m from M has a list of Rank Order List:
        - all TAs are present in this list
        - they are ordered based on how much do they fit the module preferences
    */

    // Each t from

    // Order the module preferences of TAs based on their wwights

    // allocate:

        /* Assign TA to first choice of module if not:
            - number of needed TAs for this module is met
        */



    // End of allocate
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
                // Create an object of the module_ROL to store the TAs details
                $ta_rank_details = new ModuleRankOrderList();

                // Set basic attributes to the object: ModuleRankOrderList()
                $ta_rank_details->module_id = $module->module_id;
                $ta_rank_details->academic_year = $current_academic_year;
                $ta_rank_details->ta_email = $ta->ta_email;

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

                        $did_before_weight = $weights_class->calculateRepetitionWeightForModuleForTa($ta->ta_email, $module->module_id);

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
                    // $ta_rank_details->save();

                    return redirect('/allocations')->with('success', 'Modules Rank Order List Created');
                }

            } // End of foreach TA

        } // end of foreach module
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
