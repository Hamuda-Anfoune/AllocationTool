<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\module;
use App\ta_preference;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        // REMOVE ECCESS CODE AS WORK WAS REDIRECTED AND DISTRIBUTED BETWEEN OTHER CONTROLLERS: TA & MODULE



        // Get the current academic year
        $current_academic_year = DB::table('Academic_years')->select('year')->where('current', '=', 1)->first();

        // Get logged in user's email
        $email = session()->get('email');
        // Checking of convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if(session()->get('account_type_id')== 001)
        {
            // Get the
            return view('home');
        }
        elseif(session()->get('account_type_id')== 002)
        {

            return redirect('Module/');


            // BELOW WAS MOVED TO TACONTROLLER@INDEX
            /*
             * START OF ECCESS CODE
             */


            // $all_convenors_modules = module::where('convenor_email', '=', $email)->get();

            // Get the modules WITH SUBMITTED PREFERENCES that this convenor teaches
            $convenor_preferences = DB::table('module_preferences')
                                        ->whereExists(function ($query)
                                        {
                                            $query->select(DB::raw(100))
                                                ->from('modules')
                                                ->whereRaw('module_preferences.module_id = modules.module_id')
                                                ->where('convenor_email','=', (session()->get('email')));
                                        })
                                        ->get();
            /* DONE:
             *  Get the names of the preferenced modules
             */
            $preferenced_convenor_modules = [];
             for($i=0; $i<count($convenor_preferences); $i++)
             {
                $preferenced_convenor_modules[$i] = DB::table('modules')
                                                        ->where('module_id','=', $convenor_preferences[$i]->module_id)
                                                        ->where('academic_year','=', $current_academic_year->year)
                                                        ->first();
             }

            /* DONE:
             *  Get the modules WITHOUT SUBMITTED PREFERENCES that this convenor teaches:
             *   modules that are taught by this convenor
             *   where the academic year = current academic year ->
             *   which do not exist in module_preferences ->
             */
            $nonpreferenced_convenor_modules = DB::table('modules')
                                                            ->where('convenor_email','=', $email)
                                                            ->where('academic_year','=', $current_academic_year->year)
                                                            ->whereNotExists(function ($query)
                                                            {
                                                                $query->select(DB::raw(100))
                                                                    ->from('module_preferences')
                                                                    ->whereRaw('module_preferences.module_id = modules.module_id');
                                                            })
                                                            ->get();

            return view('home')
                            ->with('preferenced_convenor_modules', $preferenced_convenor_modules)
                            // ->with('preferenced_convenor_modules', $convenor_preferences)
                            ->with('nonpreferenced_convenor_modules', $nonpreferenced_convenor_modules);

            /*
             * END OF ECCESS CODE
             */

        }
        elseif(session()->get('account_type_id')== 003 || session()->get('account_type_id')== 004)
        {
            // redirect to TA controller
            return redirect('TA/');

            // BELOW WAS MOVED TO TACONTROLLER@INDEX
            /*
             * START OF ECCESS CODE
             */

            // Get the TA's preferences
            $TAs_preferences = ta_preference::where('ta_email', '=', $email)->get();

            return view('home')->with('TAs_preferences', $TAs_preferences);

            /*
             * END OF ECCESS CODE
             */
        }
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }
}
