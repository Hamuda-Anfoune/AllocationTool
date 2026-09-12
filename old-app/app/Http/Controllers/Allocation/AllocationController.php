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
        return redirect('/admin/allocations')->with('Success', 'TA roles have already been allocated for the current semester. Please check below!');

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

        // Check if an allocation exists for this year
        if($allocation_class->allocationExistsForYear($academic_year))
        return redirect('/admin/allocations')->with('Success', 'TA roles have already been allocated for the current semester. Please check below!');

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

        session()->flash('allocated', 'true');
        return redirect('/admin/allocations')->with('allocated', 'Teaching assistants roles were allocated successfully!');
    }


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
                ->with('allocation_id', $allocation_id)
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
        // if no id was provided delete current year's allocation
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

        return redirect('/admin/dashboard')->with('success', 'Allocation '. $allocation_id . ' was deleted successfully!');
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
