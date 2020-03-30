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
        // Send with data based on the user type
        // If a convenor: send with modules he/she teaches
        // If a TA: send with preferences
        // If Admin:

        // Get the current academic year
        $current_academic_year = DB::table('Academic_years')->where('current', '=', 1)->first();

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
            // $all_convenors_modules = module::where('convenor_email', '=', $email)->get();



            // // Get the modules WITH PREFERENCES that this convenor teaches
            // $preferenced_convenor_modules = DB::table('modules')
            //                                                 ->where('convenor_email','=', $email)
            //                                                 ->whereExists(function ($query) {
            //                                                     $query->select(DB::raw(100))
            //                                                         ->from('module_preferences')
            //                                                         ->whereRaw('module_preferences.module_id = modules.module_id')
            //                                                         ->where('module_preferences.academic_year',' =', $current_academic_year);
            //                                                 })
            //                                                 ->get();


            // Get the modules WITH PREFERENCES that this convenor teaches
            $preferenced_convenor_modules = DB::table('module_preferences')
                                                            ->whereExists(function ($query) {
                                                                $query->select(DB::raw(100))
                                                                    ->from('modules')
                                                                    ->whereRaw('module_preferences.module_id = modules.module_id')
                                                                    ->where('convenor_email','=', (session()->get('email')));
                                                                })
                                                            ->get();

            // Get the modules WITHOUT PREFERENCES that this convenor teaches
            $nonpreferenced_convenor_modules = DB::table('modules')
                                                            ->where('convenor_email','=', $email)
                                                            ->whereNotExists(function ($query) {
                                                                $query->select(DB::raw(100))
                                                                    ->from('module_preferences')
                                                                    ->whereRaw('module_preferences.module_id = modules.module_id')
                                                                    ->where('module_preferences.academic_year',' =', '$current_academic_year');
                                                            })
                                                            ->get();

            return view('home')
                            ->with('preferenced_convenor_modules', $preferenced_convenor_modules)
                            ->with('nonpreferenced_convenor_modules', $nonpreferenced_convenor_modules);
        }
        elseif(session()->get('account_type_id')== 003 || session()->get('account_type_id')== 004)
        {
            // Get the TA's preferences
            $TAs_preferences = ta_preference::where('ta_email', '=', $email)->get();

            return view('home')->with('TAs_preferences', $TAs_preferences);
        }
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }
}
