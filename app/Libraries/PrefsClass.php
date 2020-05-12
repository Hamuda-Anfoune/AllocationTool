<?php

namespace App\Libraries;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\TA_preference;
use App\Ta_language_choice;
use App\ta_module_choice;
use App\used_langauge;
use App\module_preference;

class PrefsClass
{
    function taPreferenceExists($preference_id)
    {
        return (ta_preference::where('preference_id', '=', $preference_id)->count() > 0);
    }

    function clear_ta_language_choices($preference_id)
    {
        //
        return DB::table('ta_language_choices')->where('preference_id', '=', $preference_id)->delete();
    }

    function clear_ta_module_choices($preference_id)
    {
        //
        return DB::table('ta_module_choices')->where('preference_id', '=', $preference_id)->delete();
    }

    function clear_ta_preference($preference_id)
    {
        //
        return DB::table('ta_preferences')->where('preference_id', '=', $preference_id)->delete();
    }

    function storeTaPrefrences($request, $preference_id)
    {
        $email = session()->get('email');
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
        $ta_pref->have_tier4_visa = ($request->input('have_tier4_visa') == Null) ? false : true;


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


        // -------------------------------------
        //      LANGUAGE PREFERENCES START
        // -------------------------------------


        // Creating an array to save ta_module_choice instances
        $preferred_languages_array = [];
        // Counting the lenght of the array
        // $array2Length = count($preferred_languages_array);

        //for loop: Check module choices and save
        for($i=1; $i<=7; $i++)
        {
            // Creating an instance of the ta_language_choice
            $preferred_language_choice = new Ta_language_choice();

            // Will get IDs of submitted choices
            $preferred_language_choice_id =  $request->input('preferred_language_'.$i.'_id');


            if($preferred_language_choice_id != NULL) // if ID is not null
            {
                $preferred_language_choice->preference_id = $preference_id;
                $preferred_language_choice->language_id = $preferred_language_choice_id;

                // echo $preferred_language_choice;


                // ADD TO ARRAY
                $preferred_languages_array[] = $preferred_language_choice;
                // }
            }
            else // once we get a null preferred_languages_array_id
            {
                // Do nothing
            }
        }

        // ------------------------------------
        //      LANGUAGE PREFERENCES END
        // ------------------------------------

        // return $preferred_languages_array;

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

            // Save to TA language choices
            // get the length of the array where language choices are saved
            $array2Length = count($preferred_languages_array);

            // use legth to loop and save language choices
            for($l = 0; $l < $array2Length; $l++)
            {
                    $ta_preferred_languages_to_save = $preferred_languages_array[$l];
                    $ta_preferred_languages_to_save->save();
            }
        }
        catch (QueryException $e)
        {
            return false;
        }
        return true;
    }

    function storeModulePreferences($request)
    {
        // creating a new instance of Module_preference to save to DB
        $module_pref = new module_preference();

        // Adding the posted attributes to the created instance
        $module_pref->module_id = $request->input('module_id');
        $module_pref->no_of_assistants = abs(ceil($request->input('no_of_assistants')));
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
        /*
        * All saving will be in the try catch
        */
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
            return $e; //
        }

        return true;
    }
}
