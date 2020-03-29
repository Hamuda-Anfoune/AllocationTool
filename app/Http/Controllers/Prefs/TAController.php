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
use App\TA_preference;
use Illuminate\Database\QueryException;
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
        //
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
            // Get a list of all midules in database
            $modules = Module::all();

            // Get current year => method 1
            // $current_academic_year = Academic_year::all()->where('current', '1');                    // Returns a complex JSON object

            // Get current year => method 2
            $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();    // - first(): Returns a simple object,
                                                                                                        // - get():   Returns a JSON array
            // echo $current_academic_year->year;

            // pass modules to view, will be shown in a select element in the view
            return view ('preferences.ta')->with('modules', $modules)->with('current_academic_year', $current_academic_year);
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
            'semester' => ['required'],

            // Only one module choice is mandatory
        ]);


        // $request->session()->flashInput($request->all());
        // session()->flash('max_modules', $request->input('max_modules'));
        // $request->flashExcept('academic_year');


        // Get username form session, NOT WHOLE EMAIL
        // Will be used to create  preference_id
        $email = session()->get('email');
        $username_not_whole_email = substr($email, 0, strpos($email, "@"));


        // Create a preference ID using:
        /*
            username_not_whole_email+Y+academicYear+S+semester
        */
        $preference_id = $username_not_whole_email.'Y'.$request->input('academic_year').'S'.$request->input('semester');


        // Check, if TA already submitted preferences for this semester then interrupt, and advice to go to update preferences
        // One way is by checking if preference ID exists

        // if(DB::table('ta_preferences')->where('preference_id', '=', $preference_id))
        if(ta_preference::where('preference_id', '=', $preference_id)->count() > 0)
        {
                return back()->withInput($request->input())->with('alert', 'Sorry, seems like you have already submitted your prefernces for this semester!'); //
        }
        else
        {
            /*
             Handling ta_preferences table data
            */

            // creating a new instance of Module_preference to save to DB
            $ta_pref = new ta_preference();

            // Adding the posted attributes to the created instance
            $ta_pref->preference_id = $preference_id;
            $ta_pref->ta_email = $email;
            $ta_pref->max_modules = $request->input('max_modules');
            $ta_pref->max_contact_hours = $request->input('max_contact_hours');
            $ta_pref->max_marking_hours = $request->input('max_marking_hours');
            $ta_pref->academic_year = $request->input('academic_year');
            $ta_pref->semester = $request->input('semester');
            // $ta_pref->have_tier4_visa = $request->input('have_tier4_visa');

            $ta_pref->have_tier4_visa = ($request->input('have_tier4_visa') == Null) ? false : true;

            //  return $ta_pref;

            /*
                Handling ta_module_choices table data
            */

            /*
             * Will delete the gaps in the choice if any,
             * This will be done by creating a variabel to use for array keys
             * the variable's value will be calculated as such:
             *  counting the length of the array and adding 1 to it
             *
             * This should apply if the length of the array is 0
             */
            // Creating an array to save ta_module_choice instances
            $module_choices_array = [];
            // Counting the lenght of the array
            $arrayLength = count($module_choices_array);

            //for loop: Check module choices and save
            for($i=1; $i<=10; $i++)
            {
                // Creating an instance of the ta_module_choices
                $current_ta_module_choice = new ta_module_choice();

                // Will loop and get IDs of submitted choices
                $current_ta_module_choice_id =  $request->input('module_'.$i.'_id');

                if($current_ta_module_choice_id != NULL) // if ID is not null
                {
                    $current_ta_module_choice->preference_id = $preference_id;
                    $current_ta_module_choice->ta_email = $email;
                    $current_ta_module_choice->module_id = $request->input('module_'.$i.'_id');
                    $current_ta_module_choice->priority = $i;
                    $current_ta_module_choice->did_before = ($request->input('done_before_'.$i) == Null) ? false : true;



                    // Checking the length of the array to calculate the array key at which the instance will be saved
                    // Counting the lenght of the array
                    $arrayLength = count($module_choices_array);

                    if($arrayLength == 0) // If array is now empty
                    {
                        // Add to array WITHOUT chaning the key
                        $module_choices_array[$arrayLength] = $current_ta_module_choice;
                    }
                    else // if array is not empty
                    {
                        // Add WITH changing the key
                        $module_choices_array[$arrayLength] = $current_ta_module_choice;
                    }
                }
                else // once we get a null current_ta_module_choice_id
                {
                    // Do nothing
                }
            }

            /*
             * All saving will be in the try catch
             */
            try
            {
                // Save to ta_preferences table
                $ta_pref->save();

                // Save to ta_module_choices table
                for($j = 0; $j <= $arrayLength; $j++)
                {
                        $ta_module_choice_to_save = $module_choices_array[$j];
                        $ta_module_choice_to_save->priority = $j+1;
                        $ta_module_choice_to_save->save();
                }
            }
            catch (QueryException $e)
            {
                return back()->withInput($request->input())->with('alert', 'Error saving the preferences, please try again. Error: '. $e); //
            }

            // Getting the convenor email from the session sign in data
            // $ta_pref->convenor_email = session()->get('email'); // NOW IN THE MODULE TABLE, IT IS A PROPERTY NOT A PREFERENCE

            // ALL THREE STATEMENTS BELOW WORK!
            // echo $request->session()->get('email');
            // echo session()->get('email');
            // echo session('email');
            // session()->flash('Success', 'Preferences saved successfully!');

            return redirect('/preferences/ta')->with('success', 'Preference Stored'); // success: type of message, Preference Created: msg body
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
