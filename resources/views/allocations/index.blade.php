@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Allocation') }}</h4></div>
                <div class="card-body">
                    <i class="fab fa-balance-scale fa-sm fa-fw"></i>
                    <strong>Admin home page!</strong><br>
                    <ul>
                        <li>Will show data about algorithm configuration</li>
                        <li>Data will show in button cards</li>
                        <li>Once clicked, button cards will show modals</li>
                        <li>Modals will update database</li>
                        <li>Thank you!</li>
                    </ul>
                    <br>
                </div>
            </div>
            <div style="height:4vw"></div>
        </div>
    </div>

    <div class=" pt-3 pb-5 mb-3 bg-white col-md-12 text-center ">  {{-- ALLOCATE DIV --}}
        <h4>Intitate allocation process</h4>
        <div class="mb-3">
            <a class=" col-md-8 btn btn-success" href="{{ route('createModuleROLs') }}"> Allocate</a>
        </div>
    </div>

    <div class=" pt-3 pb-5 mb-3 bg-white col-md-12 text-center ">  {{-- ALLOCATE DIV --}}
        <h4>Clear Allocation Results</h4>
        {{-- <div class=" col-md-12 row"> --}}
            <div class="col-md-12 mb-3 text-center">
                <a class=" col-md-6 btn btn-danger" href="{{ route('deleteAllocation') }}">Delete Current Year's Allocation</a>
            </div>
            <div class="col-md-12 text-center">
                <a class=" col-md-6 btn btn-danger" href="{{ route('deleteModuleROLs') }}">Delete Current Year's Module ROLs</a>
            </div>
        {{-- </div> --}}
    </div>

    <div class="row md-col-12"> {{-- START OF BOARD NO. 1 --}}
        <div class="col-md-3">
            <div id="langs" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Module Convenors</p>
                </div>
            </div>
            <div id="no-of-convenors" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="/university-users/create">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Active Module Convenors</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">15</span>
                    </span>
                </a>
            </div>
            <div id="lang-weight-2" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="/university-users/create">
                    <span class=" hyperspan">
                        <p class="widget-title">Covenors With Prefs</p>
                        <hr>
                        Happy Days!
                        <br>
                        hahaha
                    </span>
                </a>
            </div>
            <div id="lang-weight-3" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="/university-users/create">
                    <span class=" hyperspan">
                        <p class="widget-title">Covenors Without Prefs</p>
                        <hr>
                        Happy Days!
                        <br>
                        hahaha
                    </span>
                </a>
            </div>
        </div>    {{-- END OF 1ST COLUMN --}}

        <div class="col-md-3">    {{-- START OF 2ND COLUMN --}}
            <div id="done-before-weight-head" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Teaching Assistants</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-green mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Active Teaching Assistants</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-green mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">TAs With Prefs</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-green mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">TAs Without Prefs</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 2ND COLUMN --}}

        <div class="col-md-3">  {{-- START OF 3RD COLUMN --}}
            <div id="lang-weight-primary" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Modules</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Modules This Year</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Modules With Prefs</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Modules Without Prefs</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 3RD COLUMN --}}

        <div class="col-md-3">  {{-- START OF 4TH COLUMN --}}
            <div id="lang-weight-primary" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Academic Year</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" data-toggle="modal" data-target="current_year_modal" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Current Academic Year</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">32</span>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 4TH COLUMN --}}
    </div> {{-- END OF BOARD NO. 1 --}}

    <div class="row md-col-12"> {{-- START OF BOARD NO. 2 --}}
        <div class="col-md-12"> {{-- SCREEN-FULL HEADER --}}
            <div id="lang-weight-head" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-12 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                    <p class="widget-title-number-col-4 ml-3">Programming Languages Weights</p>
                </div>
            </div>
        </div> {{-- END OF SCREEN-FULL HEADER --}}

        <div class="col-md-4"> {{-- DIVISION FACTORS --}}
            <div id="lang-weight-1" class="widget-div bg-darkskyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Division Factors</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div> {{-- END OF DIVISION FACTORS --}}
        <div class="col-md-4"> {{-- 1ST LANGUAGE CHOICE --}}
            <div id="lang-weight-1" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Convenor's 1st Choice</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 1ST LANGUAGE CHOICE --}}

        <div class="col-md-4"> {{-- 2ND LANGUAGE CHOICE --}}
            <div id="lang-weight-2" class="widget-div bg-darkturquoise mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Convenor's 2nd Choice</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 2ND LANGUAGE CHOICE --}}

        <div class="col-md-4"> {{-- 3RD LANGUAGE CHOICE --}}
            <div id="lang-weight-3" class="widget-div bg-olive mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Convenor's 3rd Choice</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 3RD LANGUAGE CHOICE --}}

        <div class="col-md-4"> {{-- 4TH LANGUAGE CHOICE --}}
            <div id="lang-weight-4" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Convenor's 4th Choice</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 4TH LANGUAGE CHOICE --}}

        <div class="col-md-4"> {{-- 5TH LANGUAGE CHOICE --}}
            <div id="lang-weight-5" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p>Convenor's 5th Choice</p>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 1st Preference</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">00</span>
                            </div>
                            <div class="d-flex align-content-center flex-wrap justify-content-center">
                                <p>As TA's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 5TH LANGUAGE CHOICE --}}
    </div> {{-- END OF BOARD NO. 2 --}}


    {{-- START OF MODALS --}}
    {{-- MODAL 1 --}}
    <div class="modal fade" id="current_year_modal1" tabindex="-1" role="dialog" aria-labelledby="current_year_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="current_year_modal_label">Update 1st Language Choice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="lang-1-weight" class="col-form-label">New Weight:</label>
                            <input type="text" class="form-control" id="language_weight_1" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Send message</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 1 --}}

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Send message</button>
                </div>
            </div>
        </div>
    </div>
    {{-- END OF MODALS --}}
</div>
@endsection
