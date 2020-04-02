<?php

namespace App\Http\Controllers\Prefs;

use App\Academic_year;
use App\Http\Controllers\Controller;
use App\language;
use App\used_langauge;
// use Illuminate\Foundation\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\module; // BRINGING THE MODEL
use App\module_preference;
use Illuminate\Database\Eloquent\Model;
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
        // echo session('account_type_id');

        // Checking of convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if (session()->get('account_type_id') == 002) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            // Get the convenor's email
            $convenor_email = session()->get('email');
            // Get a list of all midules in database
            $modules = Module::all()->where('convenor_email', '=', $convenor_email);

            $languages = language::all();

            // Get current year => method 1
            // $current_academic_year = Academic_year::all()->where('current', '1');                    // Returns a complex JSON object
            // echo $current_academic_year;

            // Get current year => method 2
            $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();    // - first(): Returns a simple object,
                                                                                                        // - get():   Returns a JSON array
            // echo $current_academic_year->year;

            // pass modules to view, will be shown in a select element in the view
            return view ('preferences.module')
                                ->with('modules', $modules)
                                ->with('current_academic_year', $current_academic_year)
                                ->with('languages', $languages);

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
            // 'semester' => ['required'],
        ]);

        // Get current academic year
        $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();    // - first(): Returns a simple object,

        // check if a preference for this module in this year is submitted
        if(DB::table('module_preferences')
                                    ->where('academic_year', '=', $current_academic_year->year)
                                    ->where('module_id','=', $request->input('module_id'))->exists())
        {
            return redirect('/preferences/module')->with('alert', 'Preference already submitted for this module for this academic year');
        }
        else
        {
            // creating a new instance of Module_preference to save to DB
        $module_pref = new module_preference();

        // Adding the posted attributes to the created instance
        $module_pref->module_id = $request->input('module_id');
        $module_pref->no_of_assistants = $request->input('no_of_assistants');
        $module_pref->no_of_contact_hours = $request->input('no_of_contact_hours');
        $module_pref->no_of_marking_hours = $request->input('no_of_marking_hours');
        $module_pref->academic_year = $request->input('academic_year');
        // $module_pref->semester = $request->input('semester');


        ////////////
            // Creating an array to save ta_module_choice instances
            $used_languages_array = [];
            // Counting the lenght of the array
            $arrayLength = count($used_languages_array);

            //for loop: Check module choices and save
            for($i=1; $i<=3; $i++)
            {
                // Creating an instance of the ta_module_choices
                $used_language_choice = new used_langauge();

                // Will loop and get IDs of submitted choices
                $used_languages_choice_id =  $request->input('language_'.$i.'_id');

                if($used_languages_choice_id != NULL) // if ID is not null
                {
                    $used_language_choice->module_id = $request->input('module_id');
                    $used_language_choice->language_id = $used_languages_choice_id;
                    $used_language_choice->academic_year = $current_academic_year->year;

                    // Checking the length of the array to calculate the array key at which the instance will be saved
                    // Counting the lenght of the array
                    $arrayLength = count($used_languages_array);

                    if($arrayLength == 0) // If array is now empty
                    {
                        // Add to array WITHOUT chaning the key
                        $used_languages_array[$arrayLength] = $used_language_choice;
                    }
                    else // if array is not empty
                    {
                        // Add WITH changing the key
                        $used_languages_array[$arrayLength] = $used_language_choice;
                    }
                }
                else // once we get a null used_languages_array_id
                {
                    // Do nothing
                }
            }

            // $arrayLength = count($used_languages_array);
            /*
             * All saving will be in the try catch
             */
            try
            {
                $used_language_to_save = new used_langauge();
                // Save to module_preferences table
                $module_pref->save();

                // If languages have been chosen
                if(sizeof($used_languages_array) != 0)
                {
                    // Save to ta_module_choices table
                    for($j = 0; $j <= $arrayLength; $j++)
                    {
                        $used_language_to_save = $used_languages_array[$j];
                        $used_language_to_save->save();
                    }
                }
            }
            catch (QueryException $e)
            {
                return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: '. $e); //
            }

            ////////////////////

        // $module_pref->save();
        return redirect('/preferences/module')->with('success', 'Preference Created'); // success: type of message, Preference Created: msg body
        }

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
