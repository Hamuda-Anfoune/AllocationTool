<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Academic_year;

class AcademicYearController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        // Clear current
        Academic_year::where('current','=', 1)->update(['current' => 0]);
        // Set this as current
        Academic_year::where('year','=', $req['new_academic_year'])->update(['current' => 1]);

        return back()->with('success', 'Current academic year updated to ' . $req['new_academic_year']);
    }
}
