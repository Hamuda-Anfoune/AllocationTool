<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\language;
use App\Libraries\AllocationsClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\module; // BRINGING THE MODULE MODEL
use App\Libraries\BasicDBClass;
use App\Libraries\PrefsClass;
use Illuminate\Routing\Route;

class moduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * Does the same as showAll() below.
     *
     * This returns results for the current year.
     *
     * This handles get requests
     * showAll() handles post requests for a specific year
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $basic_db_class = new BasicDBClass();
        $academic_year = $basic_db_class->getCurrentAcademicYear();

        // Get modules With prefs for  year
        $modules_without_prefs = $basic_db_class->getModulesWithoutPrefsForYear($academic_year);
        $modules_with_prefs = $basic_db_class->getModulesWithPrefsForYear($academic_year);
        $academic_years = $basic_db_class->getAllAcademicYears();

        return view('preferences.all_modules')
                ->with('academic_year', $academic_year)
                ->with('academic_years', $academic_years)
                ->with('modules_with_prefs', $modules_with_prefs)
                ->with('modules_without_prefs', $modules_without_prefs);
    }

    /**
     * Display a listing of the resource.
     * Does the same as index() above.
     *
     * This returns results for requested year.
     *
     * This handles post requests
     * index() handles get requests
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll(request $req)
    {
        $basic_db_class = new BasicDBClass();
        $academic_year = $req['academic_year'];

        // return $academic_year;`
        // Get modules With prefs for  year
        $modules_without_prefs = $basic_db_class->getModulesWithoutPrefsForYear($academic_year);
        $modules_with_prefs = $basic_db_class->getModulesWithPrefsForYear($academic_year);
        $academic_years = $basic_db_class->getAllAcademicYears();

        return view('preferences.all_modules')
                ->with('academic_year', $academic_year)
                ->with('academic_years', $academic_years)
                ->with('modules_with_prefs', $modules_with_prefs)
                ->with('modules_without_prefs', $modules_without_prefs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Checking if convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if (session()->get('account_type_id') == 002) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            $basic_db_class = new BasicDBClass();

            // Get the convenor's email
            $convenor_id = session()->get('email');

            // Get current year => method 2
            $current_academic_year = $basic_db_class->getCurrentAcademicYear();

            // Get a list of all midules in database
            $convenor_modules_without_prefs =  $basic_db_class->getModulesWithoutPrefsForConvenorForYear($convenor_id, $current_academic_year);

            // Get a list of the programming languages
            $languages = language::all();

            // pass modules to view, will be shown in a select element in the view
            return view ('Module.add')
                            ->with('modules', $convenor_modules_without_prefs)
                            ->with('current_academic_year', $current_academic_year)
                            ->with('languages', $languages);

        }
        else
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

        // Check if signed in user is a convenor
        // if not reroute to home
        //
        $this->validator($request->all())->validate();

        $prefs_class = new PrefsClass();
        $basic_db_class = new BasicDBClass();

        // Get current academic year
        $current_academic_year = $basic_db_class->getCurrentAcademicYear();

        // check if a preference for this module in this year is submitted
        if(DB::table('module_preferences')
                    ->where('academic_year', '=', $request->input('academic_year'))
                    ->where('module_id', '=', $request->input('module_id'))
                    ->exists())
        {
            return back()
                    ->withInput()
                    ->with('alert', 'Preference already submitted for'. $request->input('module_id') . ' for this academic year');
        }
        else
        {
            $saving_status = $prefs_class->storeModulePreferences($request);

            if($saving_status)
            {
                return redirect('/module/convenor')->with('success', 'Preference Saved');
            }
            else
            {
                return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: ' . $saving_status);
            }






            /*
                // creating a new instance of Module_preference to save to DB
                $module_pref = new module_preference();

                // Adding the posted attributes to the created instance
                $module_pref->module_id = $request->input('module_id');
                $module_pref->no_of_assistants = abs($request->input('no_of_assistants'));
                $module_pref->no_of_contact_hours = abs(ceil($request->input('no_of_contact_hours')));
                $module_pref->no_of_marking_hours = abs(ceil($request->input('no_of_marking_hours')));
                $module_pref->academic_year = $request->input('academic_year');
                // $module_pref->semester = $request->input('semester');


                ////////////
                // Creating an array to save ta_module_choice instances
                $used_languages_array = [];
                // Counting the lenght of the array
                $arrayLength = count($used_languages_array);

                //for loop: Check module choices and save
                for($i=1; $i<=7; $i++)
                {
                    // Creating an instance of the ta_module_choices
                    $used_language_choice = new used_langauge();

                    // Will loop and get IDs of submitted choices
                    $used_languages_choice_id =  $request->input('language_'.$i.'_id');

                    if($used_languages_choice_id != NULL) // if ID is not null
                    {
                        $used_language_choice->module_id = $request->input('module_id');
                        $used_language_choice->language_id = $used_languages_choice_id;
                        $used_language_choice->academic_year = $request->input('academic_year');

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

                // All saving will be in the try catch

                try
                {
                    $used_language_to_save = new used_langauge();
                    // Save to module_preferences table
                    $module_pref->save();

                    // If languages have been chosen
                    if(sizeof($used_languages_array) > 0)
                    {
                        // Save to ta_module_choices table
                        for($j = 0; $j <= $arrayLength; $j++)
                        {
                            $used_language_to_save = $used_languages_array[$j];
                            $used_language_to_save->priority = $j+1;
                            $used_language_to_save->save();
                        }
                    }
                }
                catch (QueryException $e)
                {
                    return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: '); //
                }

                ////////////////////

                // $module_pref->save();
                return redirect('/module/convenor')->with('success', 'Preference Created'); // success: type of message, Preference Created: msg body
            */
        }

    }



    /**
     * Display the specified resource.
     *
     * @param  string  $module_id
     * @return \Illuminate\Http\Response
     */
    public function show($module_id, $academic_year)
    {
        // This will be accessed by all types of users
        // Return view based on user type
        // convenors return a view WITH ability to edit
        // Other users return view WITHOUT ability to edit

        $basic_db_class = new BasicDBClass();

        $current_academic_year = $basic_db_class->getCurrentAcademicYear();
        $module_basic_prefs = $basic_db_class->getBasicPrefsForModuleForYear($module_id, $academic_year);
        $module_language_choices = $basic_db_class->getUsedLanguagesForModuleForYear($module_id, $academic_year);

        if((strval($academic_year) == strval($current_academic_year)) && (session()->get('account_type_id') == 000 ||
                                                                          session()->get('account_type_id') == 001 ||
                                                                          session()->get('account_type_id') == 002 ))
        {
            // view WITH edit ability
            return view('Module.show')
                    ->with('academic_year', $academic_year)
                    ->with('module_basic_prefs', $module_basic_prefs)
                    ->with('module_language_choices', $module_language_choices);
        }
        else
        {
            // View WITHOUT ability to edit
            return view('preferences.show-module-prefs')
                    ->with('academic_year', $academic_year)
                    ->with('module_basic_prefs', $module_basic_prefs)
                    ->with('module_language_choices', $module_language_choices);
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($module_id, $academic_year)
    {
        // Only convenors can edit preferences
        if( session()->get('account_type_id') != 002 )
        {
            return redirect('/');
        }

        $basic_db_class = new BasicDBClass();
        $allocation_class = new AllocationsClass();
        $prefs_class = new PrefsClass();

        // If current year has been allocated
        if($allocation_class->allocationExistsForYear($academic_year))
        return back()->with('alert', 'Sorry, TA roles have been already allocated for this semester, prefernces cannot be edited anymore!');


        //Check if prefs exist, handles when prefs were deleted then user goes back in the browser and tries to edit
        if(!$prefs_class->modulePreferenceExists($module_id, $academic_year))
        {
            if(session()->get('account_type_id') == 000 || session()->get('account_type_id') == 001 )
            {
                return redirect('/modules/prefs/all')->with('alert', 'Seems this module has no submitted preferences, please check the list below!');
            }

            if(session()->get('account_type_id') == 002)
                return redirect('/module/convenor')->with('alert', 'Seems this module has no submitted preferences, please check the list below!');

        }


        $convenor_email = session()->get('email');
        $modules = Module::all()->where('convenor_email', '=', $convenor_email);
        $languages = language::all();

        $module_basic_prefs = $basic_db_class->getBasicPrefsForModuleForYear($module_id, $academic_year);
        $module_language_choices = $basic_db_class->getUsedLanguagesForModuleForYear($module_id, $academic_year);


        // view WITH edit ability
        return view('Module.edit')
                ->with('current_academic_year', $academic_year)
                ->with('modules', $modules)
                ->with('languages', $languages)
                ->with('module_basic_prefs', $module_basic_prefs)
                ->with('module_language_choices', $module_language_choices);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Check if signed in user is a convenor
        // if not reroute to home
        //

        $this->validator($request->all())->validate();

        $prefs_class = new PrefsClass();
        $basic_db_class = new BasicDBClass();

        // Get current academic year
        $current_academic_year = $basic_db_class->getCurrentAcademicYear();

        // check if a preference for this module in this year is submitted
        if(!DB::table('module_preferences')
                                    ->where('academic_year', '=', $request->input('academic_year'))
                                    ->where('module_id','=', $request->input('module_id'))->exists())
        {
            return redirect('/preferences/module')->with('alert', 'This module dis not submit preferences for this academic year');
        }
        else
        {
            // Delete previous prefs
            DB::table('used_langauges')
                ->where('academic_year', '=', $request->input('academic_year'))
                ->where('module_id','=', $request->input('module_id'))
                ->delete();

            DB::table('module_preferences')
                ->where('academic_year', '=', $request->input('academic_year'))
                ->where('module_id','=', $request->input('module_id'))
                ->delete();


            $saving_status = $prefs_class->storeModulePreferences($request);

            if($saving_status)
            {
                //Redirect based on user account type
                if( session()->get('account_type_id') == 000 || session()->get('account_type_id') == 001)
                {
                    return redirect('allocations/')->with('success', 'Preference Updated');
                }
                else
                {
                    return redirect('/module/convenor')->with('success', 'Preference Updated');
                }
            }
            else
            {
                return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: ' . $saving_status);
            }




        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($module_id, $academic_year)
    {
        // Only Admins and convenors can edit preferences
        if( session()->get('account_type_id') != 000 &&
            session()->get('account_type_id') != 001 &&
            session()->get('account_type_id') != 002 )
        {
            return redirect('/');
        }

        $basic_db_class = new BasicDBClass();
        $allocation_class = new AllocationsClass();

        // If current year has been allocated
        if($allocation_class->allocationExistsForYear($academic_year))
        {
            return back()->with('alert', 'Sorry, TA roles have been already allocated for this semester, prefernces cannot be deleted!');
        }

        // Remove used languages
        DB::table('used_langauges')
                ->where('module_id', '=', $module_id)
                ->where('academic_year', '=', $academic_year)
                ->delete();

        // Remove preference
        DB::table('module_preferences')
                ->where('module_id', '=', $module_id)
                ->where('academic_year', '=', $academic_year)
                ->delete();


        //Redirect based on user account type
        if( session()->get('account_type_id') == 000 || session()->get('account_type_id') == 001)
        {
            return redirect('allocations/')
            ->with('success', 'Preferences for ' . $module_id . ' for semester ' . $academic_year . ' have been deleted!');
        }
        else
        {
            return redirect('/module/convenor')
            ->with('success', 'Preferences for ' . $module_id . ' for semester ' . $academic_year . ' have been deleted!');
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'module_id' => ['required', 'exists:modules,module_id'],
            'no_of_assistants' => ['required'],
            'no_of_contact_hours' => ['required'],
            'no_of_marking_hours' => ['required', 'integer'],
            'academic_year' => ['required', 'exists:academic_years,year'],
            'language_2_id' => ['nullable', 'different:language_1_id'],
            'language_3_id' => ['nullable', 'different:language_1_id', 'different:language_2_id'],
            'language_4_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id'],
            'language_5_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id',
            'different:language_4_id',],
            'language_6_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id',
            'different:language_4_id', 'different:language_5_id',],
            'language_7_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id',
            'different:language_4_id', 'different:language_5_id', 'different:language_6_id'],
        ],
        [
            // 'email.exists' => 'This email is not registered as a university user.'
        ]);
    }
}
