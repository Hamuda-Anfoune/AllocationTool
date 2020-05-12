<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use App\Libraries\BasicDBClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConvenorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $basic_db_class = new BasicDBClass();
        // Get the current academic year
        $current_academic_year = $basic_db_class->getCurrentAcademicYear();
        // Get logged in user's email
        $email = session()->get('email');

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
                                                        ->where('academic_year','=', $current_academic_year)
                                                        ->first();
             }

            /* DONE:
             *  Get the modules WITHOUT SUBMITTED PREFERENCES that this convenor teaches:
             *   modules that are taught by this convenor
             *   where the academic year = current academic year ->
             *   which do not exist in module_preferences ->
             */
            $nonpreferenced_convenor_modules = $basic_db_class->getModulesWithoutPrefsForConvenorForYear($email, $current_academic_year);

            return view('Module.index')
                            ->with('current_academic_year', $current_academic_year)
                            ->with('preferenced_convenor_modules', $preferenced_convenor_modules)
                            ->with('nonpreferenced_convenor_modules', $nonpreferenced_convenor_modules);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
