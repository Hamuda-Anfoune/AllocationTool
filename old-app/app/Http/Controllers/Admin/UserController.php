<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\BasicDBClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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

        $all_users = $basic_db_class->getAllRegisteredUsers();

        return view('admin.users.index')
                ->with('title', 'All REgistered Users')
                ->with('all_users', $all_users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAllactiveAdmins()
    {
        if(session()->get('account_type_id') != '000')
        return back()->with('alert', 'Sorry, only super admins can view this information!');

        $basic_db_class = new BasicDBClass();
        $all_active_admins = $basic_db_class->getAllactiveAdmins();

        return view('admin.users.show-admins')->with('all_active_admins', $all_active_admins);
    }

    public function showAllActiveConvenors()
    {
        if(session()->get('account_type_id') != '000' && session()->get('account_type_id') != 001)
        return back()->with('alert', 'Sorry, only admins can view this information!');

        $basic_db_class = new BasicDBClass();
        $all_convenors = $basic_db_class->getAllActiveConvenors();

        return view('admin.users.show-convenors')->with('all_convenors', $all_convenors);
    }

    public function showAllactiveTas()
    {
        if(session()->get('account_type_id') != '000' && session()->get('account_type_id') != 001)
        return back()->with('alert', 'Sorry, only admins can view this information!');

        $basic_db_class = new BasicDBClass();
        $all_tas = $basic_db_class->getAllActiveTas();

        return view('admin.users.show-tas')->with('all_tas', $all_tas);
    }

    public function showTasWithoutPrefsForYear($academic_year)
    {
        if(session()->get('account_type_id') != '000' && session()->get('account_type_id') != 001)
        return back()->with('alert', 'Sorry, only admins can view this information!');

        $basic_db_class = new BasicDBClass();

        if ($academic_year == null) {
            $academic_year = $basic_db_class->getCurrentAcademicYear();
        }
        $tas_without_prefs = $basic_db_class->getActiveTasWithoutPrefsForYear($academic_year);

        return view('admin.users.tas-without-prefs')->with('tas_without_prefs', $tas_without_prefs);
    }

    public function showConvenorsWithoutPrefsForYear($academic_year)
    {
        if(session()->get('account_type_id') != '000' && session()->get('account_type_id') != 001)
        return back()->with('alert', 'Sorry, only admins can view this information!');

        $basic_db_class = new BasicDBClass();

        if ($academic_year == null) {
            $academic_year = $basic_db_class->getCurrentAcademicYear();
        }

        $Convenors_without_prefs_with_modules = $basic_db_class->getConvenorsWithoutPrefsWithModulesForYear($academic_year);

        return view('admin.users.convenors-without-prefs')
                    ->with('Convenors_without_prefs_with_modules', $Convenors_without_prefs_with_modules);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($email)
    {

        try {
            DB::table('users')->where('email', '=', $email)->delete();
            return back()->with('success', 'User deleted');

          } catch (\Illuminate\Database\QueryException $e) {
            //   var_dump($e->errorInfo);
            return back()->with('alert', 'Sorry, this user has already submitted data which the system used to generate allocation data,
                                the user cannot be deleted before deleting all related data!');
          }

    }
}
