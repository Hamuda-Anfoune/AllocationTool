<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\WeightsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Configuration - Dashbord';
        $weights_class = new WeightsClass();
        /* TODO:
         *  Get number of active TAs
         *  Get number of active Convenors
         *  Get weights of done before weight: $module_repeatition_weights
         *  Get weights of language priority to modules: $language_weights
         *  Get weights of module priority to TAs: $module_priority_weights
         *
         *  Filter and organise this data and send to view
         */

        $module_repeatition_weights = $weights_class->getOneModuleRepetitionWeight(1);
        $language_weights = $weights_class->getWeightForAllLanguagePriorities();
        // $weighing_factors = DB::table('weighing_factors')->where('type', '=', 'current')->get();
        $module_priority_weights = $weights_class->getWeightsForAllModulePriorities();

        return view('configurations.index')->with('module_repeatition_weights',$module_repeatition_weights)
                                            ->with('language_weights',$language_weights)
                                            ->with('rank_order_list_weights',$module_priority_weights)
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

    public function updateWeighingFactors(Request $request)
    {
        // VALIDATE

        // read the set of langauges for TA form DB

        // get the set of language weights from the request

        // if applicable:
            // update division factors based on new weights
            // There should be a tickbox to recalculate division factors or not
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