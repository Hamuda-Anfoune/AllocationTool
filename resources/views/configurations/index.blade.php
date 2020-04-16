@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header text-md-center"><h4>{{ __('Module Preferences') }}</h4></div> --}}
                <div class="card-body">
                    <div class="m-5 row">
                        <div style="width:4%"><i class="fab fa-balance-scale fa-sm fa-fw" style="font-size:23px"></i></div>
                        <div style="width:96%">
                            <h4><strong>NOTES!</strong></h4><br>
                            <h5><strong>WHAT?</strong></h5>
                            The entries below are used in a weighing process.<br>
                            The process aims to create a Rank Order List of preferred TAs for each module.<br>
                            The process targets one module at a time.<br>
                            It compares the target module's preferences with the ones of each TA, i.e.<br>
                            <ul>
                                <li>The rank of the target module in the target TA's Rank Order List.</li>
                                <li>Whether or not the target TA has assisted with the terget module before.</li>
                                <li>programming languages priority list.</li>
                            </ul>
                            The level of accordance with each of the preferences awards the TA certain weights.<br>
                            The target module will have a Rank Order List, and the TA with the most weight should be on the top.<br>
                            <br>
                            <h5><strong>HOW?</strong></h5>
                            <strong>Rank Order List:</strong><br>
                            A specific value will be added to the TA's total weight based on the order of the target module in the TA's Rank Oreder List.<br>
                            <br>
                            <strong>Module Repetition Weight:</strong><br>
                            This value will be added to the TA's total weihgt if the TA has assisted with the target module before.<br>
                            <br>
                            <strong>Programming Language Priority List:</strong><br>
                            Each TA's preferred programming languages will be compared against the module's; if preferred languages match, the TA will be
                            awarded weight based on the priority of the programming language for the module.<br>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height:4vw"></div>
        </div>
    </div>

    <div class="row md-col-12"> {{-- START OF BOARD NO. 1 --}}
        <div class="col-md-3">
            <div id="langs" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Programming Languages</p>
                </div>
            </div>
            <div id="no-of-convenors" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Module Convenors</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">15</span>
                    </span>
                </a>
            </div>
            <div id="lang-weight-2" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <p class="widget-title">2nd Language Choice</p>
                        <hr>
                        Happy Days!
                        <br>
                        hahaha
                    </span>
                </a>
            </div>
            <div id="lang-weight-3" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <p class="widget-title">3rd Language Choice</p>
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
                    <p class="widget-title">Done Before</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-green mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Teaching Assistants</p>
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
                    <p class="widget-title">Programming Languages</p>
                </div>
            </div>
            <div id="lang-weight-first" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="http://www.google.com">
                    <span class=" hyperspan">
                        <p class="widget-title">1st Language Choice</p>
                        <hr>
                        Happy Days!
                        <br>
                        hahaha
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
                    <p class="widget-title">Programming Languages</p>
                </div>
            </div>
            <div id="lang-weight-first" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <p class="widget-title">1st Language Choice</p>
                        <hr>
                        Happy Days!
                        <br>
                        hahaha
                    </span>
                </a>
            </div>
        </div> {{-- END OF 4TH COLUMN --}}
    </div> {{-- END OF BOARD NO. 1 --}}

    <div class="row md-col-12"> {{-- START OF BOARD NO. 2 --}}
        <div class="col-md-12"> {{-- SCREEN-FULL HEADER --}}
            <div id="lang_weight_head" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-12 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-balance-scale fa-2x"></span>
                    <p class="widget-title-number-col-4 ml-3">Programming Languages Weights &amp; Weigning Factors</p>
                </div>
            </div>
        </div> {{-- END OF SCREEN-FULL HEADER --}}


        <div class="col-md-4"> {{-- 1ST LANGUAGE CHOICE --}}
            <div id="lang_weight_1" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" data-toggle="modal" data-target="#lang_modal_1">
                    <span class=" hyperspan-col-4">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <span class="my-3" style="font-size:20px"><strong>Programming Languages Weights</strong></span>
                        </div>
                        <hr>
                        <div class="row mx-1">
                            <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-4x mb-0 line-h-initial">{{$language_weights[1]}}</span>
                            </div>
                            <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                                <p>As module's 1st Preference</p>
                            </div>
                        </div>
                        <div class="row mx-1">
                            <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">{{$language_weights[2]}}</span>
                            </div>
                            <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                                <p>As module's 2nd</p>
                            </div>
                        </div>
                        <div class="row mx-1">
                            <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">{{$language_weights[3]}}</span>
                            </div>
                            <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                                <p>As module's 3rd</p>
                            </div>
                        </div>
                        <div class="row mx-1">
                            <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">{{$language_weights[4]}}</span>
                            </div>
                            <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                                <p>As module's 4th</p>
                            </div>
                        </div>
                        <div class="row mx-1">
                            <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                                <span class="fa-3x mb-0 line-h-initial">{{$language_weights[5]}}</span>
                            </div>
                            <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                                <p>As module's 5th</p>
                            </div>
                        </div>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 1ST LANGUAGE CHOICE --}}


    </div> {{-- END OF BOARD NO. 2 --}}

    {{-- START OF MODALS --}}
    {{-- MODAL 1 --}}
    <div class="modal fade" id="lang_modal_1" tabindex="-1" role="dialog" aria-labelledby="lang_modal_1_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update 1st Language Choice</h5>
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

    {{-- MODAL 2 --}}
    <div class="modal fade" id="lang_modal_2" tabindex="-1" role="dialog" aria-labelledby="lang_modal_2_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update 2nd Language Choice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="lang_2_weight" class="col-form-label">New Weight:</label>
                            <input type="text" class="form-control" id="language_weight_2" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 2 --}}

    {{-- MODAL 2 --}}
    <div class="modal fade" id="lang_modal_3" tabindex="-1" role="dialog" aria-labelledby="lang_modal_3_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update 3rd Language Choice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="lang_3_weight" class="col-form-label">New Weight:</label>
                            <input type="text" class="form-control" id="language_weight_3" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 3 --}}
    {{-- MODAL 4 --}}
    <div class="modal fade" id="lang_modal_4" tabindex="-1" role="dialog" aria-labelledby="lang_modal_4_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update 4th Language Choice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="lang_4_weight" class="col-form-label">New Weight:</label>
                            <input type="text" class="form-control" id="language_weight_4" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 4 --}}
    {{-- MODAL 5 --}}
    <div class="modal fade" id="lang_modal_5" tabindex="-1" role="dialog" aria-labelledby="lang_modal_5_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update 5th Language Choice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="lang_5_weight" class="col-form-label">New Weight:</label>
                            <input type="text" class="form-control" id="language_weight_5" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 5 --}}
    {{-- MODAL 6 --}}
    <div class="modal fade" id="weighing_factors_modal" tabindex="-1" role="dialog" aria-labelledby="weighing_factors_modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Weighing Factors</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf
                        <div class="form-group">
                            <label for="weighing_2" class="col-form-label">As TA's 2nd Choice:</label>
                            <input type="text" class="form-control" id="weighing_2" required>
                            {{-- value="{{old('weighing_2', $dog->weighing_2)}}" --}}
                        </div>
                        <div class="form-group">
                            <label for="weighing_3" class="col-form-label">As TA's 3rd Choice:</label>
                            <input type="text" class="form-control" id="weighing_3" required>
                        </div>
                        <div class="form-group">
                            <label for="weighing_4" class="col-form-label">As TA's 4th Choice:</label>
                            <input type="text" class="form-control" id="weighing_4" required>
                        </div>
                        <div class="form-group">
                            <label for="weighing_5" class="col-form-label">As TA's 5th Choice:</label>
                            <input type="text" class="form-control" id="weighing_5" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> {{-- END OF MODAL 6 --}}
    {{-- END OF MODALS --}}
</div>
@endsection
