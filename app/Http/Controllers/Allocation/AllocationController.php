<?php

namespace App\Http\Controllers\Allocation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         /* TODO:
          *  Get number of Modules
          *  Get number of convenors
          *  Get number of TAs
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
    public function create()
    {
        $Modules = DB::table('module_preferences')->select('module_id')->get();

        return $Modules;



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
