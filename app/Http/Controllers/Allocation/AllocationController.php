<?php

namespace App\Http\Controllers\Allocation;

use App\Allocation;
use App\Http\Controllers\Controller;
use App\Libraries\AllocationsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ModuleRankOrderList;
use App\Libraries\BasicDBClass;
use App\Libraries\WeightsClass;
use App\Libraries\Allocator;
use App\ta_allocation_data;
use Illuminate\Database\QueryException;

class AllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function trial()
    {

        return DB::table('modules')
                    ->select('module_id')
                    // ->where('academic_year','=',$academic_year)
                    ->whereNotExists(function($query)
                    {
                        $query->select('module_id')
                            ->from('ta_module_choices')
                            // ->where('ta_module_choices.preference_id','=','ta5Y2019-2020-02');
                            ->whereRaw('preference_id', 'ta5Y2019-2020-02');
                        })
                    ->get();


                        //////////


        return DB::table('ta_module_choices')
                        ->select('module_id')
                        ->whereRaw('preference_id = "ta5Y2019-2020-02"')
                        ->get();


                        ///////////////////


        return DB::table('modules')
                    ->select('module_id')
                    // ->where('academic_year','=',$academic_year)
                    ->whereNotExists(function($query)
                    {
                        $query->select('module_id')
                            ->from('ta_module_choices')
                            // ->where('ta_module_choices.preference_id','=','ta5Y2019-2020-02');
                            ->whereRaw('preference_id = "ta5Y2019-2020-02"');
                        })
                    ->get();

                    // DB::select('module_id FROM modules
                    // WHERE NOT EXISTS (SELECT module_id FROM ta_module_choices
                    //                   WHERE cities_stores.store_type = stores.store_type')
    }

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

        $basic_db_class = new BasicDBClass();
        $allocation_class = new AllocationsClass();

        $academicYears = $basic_db_class->getAllAcademicYears();

        $allocations = $allocation_class->getAllAllocationIds();

        return view('allocations.index')
                ->with('allocations', $allocations);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allocationData()
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

        $basic_db_class = new BasicDBClass();

        $data = [];

        $current_academic_year = $basic_db_class->getCurrentAcademicYear();
        $data['current_academic_year'] = $current_academic_year;
        $data['academicYears'] = $basic_db_class->getAllAcademicYears();
        $data['university_users_count'] = count($basic_db_class->getAllUniversityUsers());
        $data['active_users_count'] = count($basic_db_class->getAllActiveRegisteredUsers());
        $data['active_admins_count'] = count($basic_db_class->getAllactiveAdmins());
        $data['active_tas_count'] = count($basic_db_class->getAllActiveTas());
        $data['active_tas_without_prefs_count'] = count($basic_db_class->getActiveTasWithoutPrefsForYear($current_academic_year));
        $data['active_convenor_count'] = count($basic_db_class->getAllActiveConvenors());
        $data['active_convenors_missing_prefs_count'] = count($basic_db_class->getConvenorsWithoutPrefsForYear($current_academic_year));
        $data['current_year_modules_count'] = count($basic_db_class->getAllModulesForYear($current_academic_year));
        $data['current_modules_without_prefs_count'] = count($basic_db_class->getModulesWithoutPrefsForYear($current_academic_year));

        return view('allocations.dashboard')->with('data', $data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $basic_DB_access = new BasicDBClass();
        $allocation_class = new AllocationsClass();
        $allocator = new Allocator();

        $academic_year = $basic_DB_access->getCurrentAcademicYear();


        // Check if an allocation exists for this year
        if($allocation_class->allocationExistsForYear($academic_year))
        return back()->with('alert', 'TA roles have already been allocated for the current semester. Delete the old allocation before creating a new one!');

        // Get all modules without submitted preferences
        $modules_without_prefs = $basic_DB_access->getModulesWithoutPrefsForYear($academic_year);

        // Get all TAs without submitted preferences
        $tas_without_prefs = $basic_DB_access->getActiveTasWithoutPrefsForYear($academic_year);

        // Redirect to show missing prefs in case there are any!
        if(count($tas_without_prefs) > 0 || count($modules_without_prefs) > 0)
        {
            return redirect('/admin/allocations/missing-prefs');
        }

        /**
         *      CREATE AND GET ALL ROLs AND PREFS FOR THIS YEAR
         */

        $ROLs = $allocator->createModuleROLs();

        // Create ROLs
        if($ROLs != true)
            return back()->with('alert', 'Failed to create module ROLs, Please try again later!');


        // Get all TAs and their prefs and ROLS
        $all_tas_prefs_and_Rols =  $allocation_class->createTasRolsAndPrefsForYear($academic_year);

        // Get all Modules with their prefs and ROLS
        // $modules_prefs_and_ROLs =  $allocation_class->createFinalRolsForModulesForYear($academic_year);

        // Intitiate allocation matrix
        $allocations_matrix = $allocation_class->initiateAllocationsMatrix($academic_year);

        // ITEREATE THROUGH THE TAs TO ALLOCATE
        foreach($all_tas_prefs_and_Rols as $ta)
        {
            $allocations_matrix = $allocator->allocate($ta, $allocations_matrix);
        }

        $basic_db_class = new BasicDBClass();


        // while(count($allocations_matrix['removed_tas']) > 0)
        // for($k = 0; $k <= 100; $k++)
        for($k = 0; $k <= count($basic_db_class->getAllActiveTas()); $k++) //check for removed tas times the number of all the TAs
        {
            if(count($allocations_matrix['removed_tas']) > 0)
            {
                // return (string) in_array('CO1008', $allocations_matrix['ta_allocations']['gta4@gmail.com']['modules']);
                foreach($allocations_matrix['removed_tas'] as $key => $removed)
                {
                    // return $allocations_matrix['removed_tas'];
                    $target = $removed;
                    unset($allocations_matrix['removed_tas'][$key]);
                    $allocations_matrix = $allocator->allocate($target, $allocations_matrix);
                }
            }
        }

        $allocation_id = $academic_year . '-A-01';
        $creator_email = session()->get('email');

        foreach($allocations_matrix['ta_allocations'] as $ta_allocation)
        {
            // echo $ta_allocation['ta_id'] . 'contact' . $ta_allocation['contact_hours'] . 'marking' . $ta_allocation['marking_hours'];
            foreach($ta_allocation['modules'] as $allocated_module)
            {
                $allocation = new Allocation();
                $allocation->allocation_id = $allocation_id;
                $allocation->academic_year = $academic_year;
                $allocation->module_id = $allocated_module; // Module
                $allocation->ta_id = $ta_allocation['ta_id'];
                $allocation->creator_email = $creator_email;
                $allocation->save();
            }

            $ta_allocation_data = new ta_allocation_data();
            $ta_allocation_data->allocation_id = $allocation_id;
            $ta_allocation_data->academic_year = $academic_year;
            $ta_allocation_data->ta_id = $ta_allocation['ta_id'];
            $ta_allocation_data->contact_hours = $ta_allocation['contact_hours'];
            $ta_allocation_data->marking_hours = $ta_allocation['marking_hours'];
            $ta_allocation_data->save();
        }

        return redirect('/admin/allocations')->with('success', 'Teaching assistants roles allocated successfully!');
    }

    /*
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


            // TODO:

            //  Check if TA is not already assigned to Module

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
                                    //
                                    //      Remove the TA with the least weight from the allocation
                                    //
                                        // Get the min value
                                        $min_weight = min(array_column($allocations_matrix['module_allocations'][$module_choice_id]['tas'], 'weight'));

                                        // array_column creates a new array with data from the chosen column with keys from 0
                                        // array-search returns the key of the searched item from the new array created by array_column
                                        $key_to_remove = array_search($min_weight, array_column($allocations_matrix['module_allocations'][$module_choice_id]['tas'], 'weight'));

                                        // save the email of the removed TA with the min
                                        $ta_id_to_remove = $allocations_matrix['module_allocations'][$module_choice_id]['tas'][$key_to_remove]['ta_id'];

                                        // Remove TA with min weight from module's allocations
                                        unset($allocations_matrix['module_allocations'][$module_choice_id]['tas'][$key_to_remove]);

                                    //
                                    //      Remove target module from removed TA's allocations
                                    //

                                        //Get the module's key in the removed TA's allocation array
                                        $module_key_to_remove = array_search($module_choice_id, $allocations_matrix['ta_allocations'][$ta_id_to_remove]['modules']);

                                        // Remove module from removed TA's allocations
                                        unset($allocations_matrix['ta_allocations'][$ta_id_to_remove]['modules'][$module_key_to_remove]);

                                        // Remove module's working hours from TA's weekly_working_hours
                                        $allocations_matrix['ta_allocations'][$ta['ta_id']]['weekly_working_hours'] -= $modules_prefs_and_ROLs[$module_choice_id]['weekly_working_hours'];

                                    //
                                    //      Allocate current TA and module to each other
                                    //

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
    */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($allocation_id)
    {
        $allocation_class = new AllocationsClass();

        $allocation_data = $allocation_class->getAllocationById($allocation_id);
        $ta_allocation_data = $allocation_class->getTaAllocationDataById($allocation_id);

        // Print_r($allocation_data[0][1]);

        return view('allocations.show')
                ->with('allocation_data', $allocation_data)
                ->with('ta_allocation_data', $ta_allocation_data);
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
    public function destroy($allocation_id = null)
    {
        if($allocation_id == null)
        {
            $basic_db_class = new BasicDBClass();
            $allocation_class = new AllocationsClass();

            $academic_year = $basic_db_class->getCurrentAcademicYear();

            if(!$allocation_class->allocationExistsForYear($academic_year))
            return back()->with('alert', 'No allocation found for the current semester, roles have not been allocated yet or allocation has been deleted already!');

            if(!$allocation_class->deleteAllAllocationsForYear($academic_year))
            return back()->with('alert', 'Could not delete allocation, please try again later!');
        }
        else
        {
            $allocation_class = new AllocationsClass();

            if(!$allocation_class->deleteAllocationById($allocation_id))
            return back()->with('alert', 'Could not delete allocation, please try again later!');
        }

        return back()->with('success', 'Allocation deleted!');
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
