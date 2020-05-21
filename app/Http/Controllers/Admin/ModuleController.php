<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Libraries\BasicDBClass;
use App\module;

class ModuleController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $basic_db_class = new BasicDBClass();
        $current_academic_year = $basic_db_class->getCurrentAcademicYear();

        $convenors = $basic_db_class->getAllActiveConvenors();

        return view('admin.modules.add')
                ->with('current_academic_year', $current_academic_year)
                ->with('convenors', $convenors);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        // Validate data
        $this->validator($req->all())->validate();

        //create a new module entry
        if(module::create([
            'module_id' => $req['module_id'],
            'module_name' => $req['module_name'],
            'convenor_email' => $req['convenor_email'],
            'academic_year' => $req['academic_year'],
        ]))
        {
            return back()->with('success', 'Module '.$req['module_name'] . ' has been added successfully.');
        }
        else{
            return back()->with('Alert', 'Something went wrong, please try again later!.');
        }
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'module_name' => ['required', 'string', 'max:255', 'unique:modules'],
            'module_id' => ['required', 'string', 'max:255', 'unique:modules'], // foreign key validation: https://timacdonald.me/foreign-key-validation-rule/
            'academic_year' => ['required','exists:academic_years,year',  'string', 'max:255'],
            'convenor_email' => ['required','exists:university_users,email', 'string', 'min:8'],
        ]);
    }
}
