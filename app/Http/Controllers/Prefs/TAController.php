<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use App\module; // BRINGING THE MODEL
use App\ta_module_choice;
use App\language;
use App\Libraries\AllocationsClass;
use App\Libraries\BasicDBClass;
use App\Libraries\PrefsClass;
use App\Ta_language_choice;
use App\TA_preference;
use Mockery\Matcher\HasValue;

/*
 * TODO:
 *  find a way to sort the preference priority
 *
 */


class TAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $basic_DB_class = new BasicDBClass();

        // Get the current academic year
        $current_academic_year = $basic_DB_class->getCurrentAcademicYear();

        // Get logged in user's email
        $email = session()->get('email');


        // Get the TA's preferences
        $TAs_preferences = ta_preference::where('ta_email', '=', $email)
                                            ->where('academic_year', '=', $current_academic_year)
                                            ->get();

        return view('TA.index')->with('TAs_preferences', $TAs_preferences);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*
        ---------------------------------------------------------------------
        THIS PAGE SHOULD ONLY SHOW TO A SIGNED IN TA ACCOUNT
        ---------------------------------------------------------------------
        */
        // Checking of convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if (session()->get('account_type_id')== 003 || session()->get('account_type_id')== 004) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            $basic_DB_class = new BasicDBClass();


            // Get a list of all the prgorammimg languages
            $languages = language::all();

            // Get current year
            $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();

            // Get a list of all midules in database
            // $modules = Module::all();
            $modules = $basic_DB_class->getAllModulesForYear($current_academic_year->year);

            // pass modules to view, will be shown in a select element in the view
            return view ('TA.add')
                                ->with('modules', $modules)
                                ->with('current_academic_year', $current_academic_year)
                                ->with('languages', $languages);
            // return $modules;

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
        $this->validate($request, [
            // Array of rules
            // at least one module choice is required
            'module_1_id' => ['required', 'exists:modules,module_id'],
            'max_modules' => ['required'],
            'max_contact_hours' => ['required'],
            'max_marking_hours' => ['required'],
            'academic_year' => ['required', 'exists:academic_years,year'],
            // 'semester' => ['required'],

            // Only one module choice is mandatory
        ]);

        $prefs_class = new PrefsClass();

        // Get username form session, NOT WHOLE EMAIL
        // Will be used to create  preference_id
        $email = session()->get('email');
        $username_not_whole_email = substr($email, 0, strpos($email, "@"));


        // Create a preference ID using:
        /*
            username_not_whole_email+Y+academicYear
        */
        $preference_id = $username_not_whole_email.'Y'.$request->input('academic_year');


        // Check, if TA already submitted preferences for this semester then interrupt, and advice to go to update preferences
        // One way is by checking if preference ID exists in the
        if($prefs_class->taPreferenceExists($preference_id))
        {
                return back()->withInput($request->input())->with('alert', 'Sorry, seems like you have already submitted your prefernces for this semester!'); //
        }
        else
        {
            // Call method to store prefs
            if($prefs_class->storeTaPrefrences($request, $preference_id))
            {
                return redirect('TA/')->with('success', 'Preference Stored'); // success: type of message, Preference Created: msg body
            }
            else
            {
                return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try submitting again later.');
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($preference_id)
    {

        $basic_db_class = new BasicDBClass();

        // Get the preference data
        $current_ta_preferences = $basic_db_class->getTAPreferenceData($preference_id);

        // Get the module choises for the TA based on preference
        $current_module_choices = $basic_db_class->getModuleChoicesForTAForYear($preference_id);

        // Get language choices from ta_language_choices and their names from languages
         $current_language_choices = $basic_db_class->getTaLanguageChoicesForPreference($preference_id);


        return view('TA.show')
                            ->with('current_ta_preferences', $current_ta_preferences)
                            ->with('current_module_choices', $current_module_choices)
                            ->with('current_language_choices', $current_language_choices);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($preference_id)
    {
        if (session()->get('account_type_id')== 003 || session()->get('account_type_id')== 004) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            $basic_db_class = new BasicDBClass();

            // Get the preference data
            $current_ta_preferences = $basic_db_class->getTAPreferenceData($preference_id);

            // Get the module choises for the TA based on preference
            $current_module_choices = $basic_db_class->getModuleChoicesForTAForYear($preference_id);

            // Get language choices from ta_language_choices and their names from languages
            $current_language_choices = $basic_db_class->getTaLanguageChoicesForPreference($preference_id);

            // Get a list of all the prgorammimg languages
            $languages = language::all();

            // Get current year
            $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();

            // Get a list of all midules in database
            // $modules = Module::all();
            $modules = $basic_db_class->getAllModulesForYear($current_academic_year->year);

            // pass modules to view, will be shown in a select element in the view
            return view ('TA.edit')
                                ->with('current_ta_preferences', $current_ta_preferences)
                                ->with('current_module_choices', $current_module_choices)
                                ->with('current_language_choices', $current_language_choices)
                                ->with('modules', $modules)
                                ->with('current_academic_year', $current_academic_year)
                                ->with('languages', $languages);

        }else
        {
            return redirect('home');
        }

        // Suggestion
        // Pobulate a form based on a model
        // https://laravelcollective.com/docs/6.0/html#form-model-binding
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        //check if current user is GTA or TA
        if (session()->get('account_type_id') != 003 && session()->get('account_type_id') != 004) // session value is assigned in authenticated() in AuthenticatesUsers.php
        {
            // Redirect to home
            return redirect('home');
        }

        // Validate
        $this->validate($req, [
            // Array of rules
            // at least one module choice is required
            'module_1_id' => ['required','string', 'exists:modules,module_id'],
            'max_modules' => ['required', 'integer'],
            'max_contact_hours' => ['required', 'integer'],
            'max_marking_hours' => ['required', 'integer'],
            'academic_year' => ['required','string', 'exists:academic_years,year'],
            // 'semester' => ['required'],

            // Only one module choice is mandatory
        ]);

        $basic_db_class = new BasicDBClass();
        $allocation_class = new AllocationsClass();
        $prefs_class = new PrefsClass();

        // Check if requested academic year is current
        if($basic_db_class->getCurrentAcademicYear() != $req['academic_year'])
            return back()->with('alert', 'Sorry, Only current semesters\'s preferences can be edited!');

        // Check if requested year has not been allocated yet
        if($allocation_class->allocationExistsForYear($req['academic_year']))
            return back()->with('alert', 'Sorry, Cannot edit current semesters\'s preferences, TA roles have already been allocated!');


        // Get the pref ID from the req
        $preference_id = $req['preference_id'];

        // Clear the current pref from the DB
        $prefs_class->clear_ta_language_choices($req['preference_id']);
        $prefs_class->clear_ta_module_choices($req['preference_id']);
        $prefs_class->clear_ta_preference($req['preference_id']);

        // Insert the new data
        if($prefs_class->storeTaPrefrences($req, $preference_id))
        {
            return redirect('TA/')->with('success', 'Your preference have been updated!'); // success: type of message, Preference Created: msg body
        }
        else
        {
            return back()->withInput($req->input())->with('alert', 'Error updating your preferences, please try submitting again later.');
        }

        // Return
        // return redirect('/TA')->with('success', 'Your preferences updated for the acdemic year ' . $req['academic_year']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($preference_id)
    {
        $prefs_class = new PrefsClass();

        $prefs_class->clear_ta_language_choices($preference_id);
        $prefs_class->clear_ta_module_choices($preference_id);
        $prefs_class->clear_ta_preference($preference_id);

        return redirect('TA/prefs/add')->with('success', 'Your preferences have been deleted');
    }
}
