<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\BasicDBClass;
use App\Libraries\WeightsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Configuration - Dashbord';
        $weights_class = new WeightsClass();
        $basic_db_class = new BasicDBClass();
        /* TODO:
         *  Get number of active TAs
         *  Get number of active Convenors
         *  Get weights of done before weight: $module_repeatition_weights
         *  Get weights of language priority to modules: $language_weights
         *  Get weights of module priority to TAs: $module_priority_weights
         *
         *  Filter and organise this data and send to view
         */

        $current_module_repeatition_weights = $weights_class->getAllCurrentModuleRepetitionWeights();
        $language_weights = $weights_class->getWeightForAllLanguagePriorities();
        $module_priority_weights = $weights_class->getWeightsForAllModulePriorities();
        $academicYears = $basic_db_class->getAllAcademicYears();
        $current_academic_year = $basic_db_class->getCurrentAcademicYear();

        return view('configurations.index')->with('current_module_repeatition_weights',$current_module_repeatition_weights)
                                            ->with('language_weights',$language_weights)
                                            ->with('module_priority_weights',$module_priority_weights)
                                            ->with('academicYears', $academicYears)
                                            ->with('current_academic_year', $current_academic_year)
                                            ->with('title', $title);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Configuration - Edit';

        //


        return view('configurations.add');
    }

    public function updateLanguageWeights(Request $request)
    {
        // VALIDATE

        // get the new
    }

    public function updateModuleDoneBefore(Request $request)
    {
        // VALIDATE

        // get the new
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
