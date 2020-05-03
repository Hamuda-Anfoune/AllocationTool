<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Libraries\BasicDBClass;
use App\university_users;
use Illuminate\Http\Request;

class UniversityUsersController extends Controller
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
        $basic_db_class = new BasicDBClass();
        $account_types = $basic_db_class->getAccountTypes();

        return view('admin.universityUsers.add')
                ->with('account_types',$account_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $this->validator($req->all())->validate();

        //Create anew resord in table
        university_users::create([
            'email' => $req['email'],
            'account_type_id' => $req['account_type_id'],
        ]);

        return back()->with('success', 'Universtiyt user added successfully!');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'account_type_id' => ['required','exists:account_types,account_type_id',  'string', 'max:255'],
            'email' => ['required','email', 'string', 'max:255'],
        ]);
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
