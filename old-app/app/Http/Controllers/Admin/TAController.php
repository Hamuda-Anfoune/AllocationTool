<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\BasicDBClass;

class TAController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $current_account_type = session()->get('account_type_id');

        if($current_account_type == '002')
        return back()->with('alert', 'Sorry, only admins and teaching assistants can veiw this info!');

        // If user is a ta, check if ta is showing his own prefs
        if($current_account_type == '003' || $current_account_type == '004')
        {
            if(session()->get('email') != $email)
            return back()->with('alert', 'Teaching assistants can only see their own preferences!');
        }

        $basic_db_class = new BasicDBClass();
        $TAs_preferences = $basic_db_class->getAllPrefsForTAEmail($email);

        return view('admin.users.ta-all-prefs')->with('TAs_preferences', $TAs_preferences);
    }
}
