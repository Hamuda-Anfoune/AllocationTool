<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        /* TODO:
         *  Get data about done before weight
         *  Get data about language priority to TAs
         *  Get data
         *
         *  Filter and organise this data and send to view
         */

        $weight_for_done_before = DB::table('module_repeatition_weights')->select('1_time_weight')->where('type', '=', 'current')->first();
        $language_weights =  DB::table('module_repeatition_weights')->where('type', '=', 'current')->get();

        return view('configurations.index');
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
