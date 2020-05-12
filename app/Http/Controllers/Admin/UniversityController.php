<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\university_users;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;

class UniversityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    use RegistersUsers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('university.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        DB::table('universities')->insert(array([
            'name' => $data['university_name'],
            'university_email' => $data['university_email'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')]));

        university_users::create([
            'email' => $data['email'],
            'account_type_id' => '000',
        ]);

        return User::create([
            'name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'account_type_id' => '000',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'university_name' => ['required', 'string', 'max:255'],
            'university_email' => ['required', 'string', 'email', 'max:255', 'unique:universities'], // foreign key validation: https://timacdonald.me/foreign-key-validation-rule/
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
