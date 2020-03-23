<?php

namespace App\Http\Controllers\Prefs;

use App\Academic_year;
use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\module; // BRINGING THE MODEL
use App\module_preference;
use Illuminate\Routing\Route;

class moduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        /*
            ---------------------------------------------------------------------
            THIS PAGE SHOULD ONLY SHOW TO A SIGNED IN MODULE CONVENOR ACCOUNT
            ---------------------------------------------------------------------
            */
            // echo $request->session()->get('email');
            // echo session()->get('email');
            // echo session('email');

        // Checking of convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if (session()->get('account_type_id')== 001 || session()->get('account_type_id')== 002) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            // Get a list of all midules in database
            $modules = Module::all();

            // Get current year => method 1
            // $current_academic_year = Academic_year::all()->where('current', '1');                    // Returns a complex JSON object
            // echo $current_academic_year;

            // Get current year => method 2
            $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();    // - first(): Returns a simple object,
                                                                                                        // - get():   Returns a JSON array
            // echo $current_academic_year->year;

            // pass modules to view, will be shown in a select element in the view
            return view ('preferences.module')->with('module', $modules)->with('current_academic_year', $current_academic_year);

        }else
        {
            // Reroute to home
            return redirect('home');
        }
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
         ---------------------------------------------------------------------
           THIS PAGE SHOULD ONLY SHOW TO A SIGNED IN MODULE CONVENOR ACCOUNT
         ---------------------------------------------------------------------
         */


        // Check if signed in user is a convenor
        // if not reroute to home
        //

        $this->validate($request, [
            // Array of rules
            'module_id' => ['required', 'exists:modules,module_id'],

            // Add a rule to check, if module already has preference this year then interrupt

            'no_of_assistants' => ['required'],
            'no_of_contact_hours' => ['required'],
            'no_of_marking_hours' => ['required'],
            'academic_year' => ['required', 'exists:academic_years,year'],
            'semester' => ['required'],
        ]);

        // creating a new instance of Module_preference to save to DB
        $module_pref = new module_preference();

        // Adding the posted attributes to the created instance
        $module_pref->module_id = $request->input('module_id');
        $module_pref->no_of_assistants = $request->input('no_of_assistants');
        $module_pref->no_of_contact_hours = $request->input('no_of_contact_hours');
        $module_pref->no_of_marking_hours = $request->input('no_of_marking_hours');
        $module_pref->academic_year = $request->input('academic_year');
        $module_pref->semester = $request->input('semester');

        //Getting the convenor email from the session signin data
        // $module_pref->convenor_email = session()->get('email'); // NOW IN THE MODULE TABLE, IT IS A PREPERTY NOT A PREFERENCE

        // ALL THREE STATEMENTS BELOW WORK!
        // echo $request->session()->get('email');
        // echo session()->get('email');
        // echo session('email');

        $module_pref->save();
        return redirect('/preferences/module')->with('success', 'Preference Created'); // success: type of message, Preference Created: msg body
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
