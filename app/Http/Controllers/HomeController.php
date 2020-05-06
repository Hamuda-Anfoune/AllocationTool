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
        // Checking of convenor: Admin = 001, Convenor = 002, GTA = 003, Externatl TA = 004
        if(session()->get('account_type_id')== 000 || session()->get('account_type_id')== 001)
        {
            //
            return redirect('allocations/');
        }
        elseif(session()->get('account_type_id')== 002)
        {
            //
            return redirect('module/convenor');
        }
        elseif(session()->get('account_type_id')== 003 || session()->get('account_type_id')== 004)
        {
            // redirect to TA controller
            return redirect('TA/');
        }
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }
}
