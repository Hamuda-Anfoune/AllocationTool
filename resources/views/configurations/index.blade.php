@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
{{-- <div class="container"> --}}
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-md-center">
                <a class="btn btn-info btn-sm" data-toggle="collapse" href="#instructions" role="button" aria-expanded="false" aria-controls="instructions">Instructions</a>
            </div>
            <div class="card-body">
                <div class="collapse" id="instructions">
                    <i class="fab fa-balance-scale fa-sm fa-fw"></i>
                    <strong>NOTES!</strong><br>
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
                        The variables below represent the weights used.
                        The target module will have a Rank Order List, and the TA with the most weight should be on the top.<br>
                        <br>
                        <h5><strong>HOW?</strong></h5>
                        <strong>Module Priority in the TA's Rank Order List:</strong><br>
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
        <div style="height:4vw"></div>
    </div>
</div>

<div class="row md-col-12"> {{-- START OF BOARD NO. 1 --}}
    <div class="col-md-12"> {{-- SCREEN-FULL HEADER --}}
        <div id="lang_weight_head" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
            <div class="col-md-12 d-flex align-content-center flex-wrap justify-content-center">
                <span class="fas fa-balance-scale fa-2x"></span>
                <p class="widget-title-number-col-4 ml-3">Weigning Variables</p>
            </div>
        </div>
    </div> {{-- END OF SCREEN-FULL HEADER --}}

    <div class="col-md-4"> {{-- START OF FIRST COLUMN --}}
        {{-- START OF MODULE PRIORITY WEIGHTS --}}
        <div id="lang_weight_1" class="widget-div bg-skyblue mb-3 min-vh-8">
            <a class="btn col-md-12" data-toggle="modal" data-target="#module_priority_weights_modal">
                <span class=" hyperspan-col-4">
                    <div class="d-flex align-content-center flex-wrap justify-content-center">
                        <span class="my-3" style="font-size:20px"><strong>Module Priority Weights</strong></span>
                    </div>
                    <hr>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-4x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_1}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 1st Preference</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_2}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 2nd</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_3}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 3rd</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_4}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 4th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_5}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 5th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_6}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 6th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_7}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 7th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_8}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 8th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_9}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 9th</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$module_priority_weights[0]->module_weight_10}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>As TA's 10th</p>
                        </div>
                    </div>
                </span>
            </a>
        </div>
        {{-- END OF MODULE PRIORITY WEIGHTS --}}
    </div>  {{-- END OF FIRST COLUMN --}}

    <div class="col-md-4">  {{-- START OF SECOND COLUMN --}}
        {{-- START OF PROGRAMMING LANGUAGES WEIGHTS --}}
        <div id="lang_weight_1" class="widget-div bg-olive mb-3 min-vh-8">
            <a class="btn col-md-12" data-toggle="modal" data-target="#langauge_weights_modal">
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
        {{-- END OF PROGRAMMING LANGUAGES WEIGHTS --}}

        {{-- START OF CURRENT ACADEMIC YEAR --}}
        <div id="current_year" class="widget-div bg-turquoise mb-3 min-vh-8">
            <a class="btn col-md-12" data-toggle="modal" data-target="#update_year_modal">
                <span class=" hyperspan-col-4">
                    <div class="d-flex align-content-center flex-wrap justify-content-center">
                        <span class="my-3" style="font-size:20px"><strong>Current Semester</strong></span>
                    </div>
                    <hr>
                    <div class="row mx-1">
                        <div class="col-md-12 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-2x mb-0 line-h-initial">{{$current_academic_year}}</span>
                        </div>
                    </div>
                </span>
            </a>
        </div>
        {{-- END OF CURRENT ACADEMIC YEAR --}}
    </div>  {{-- END OF 2ND COLUMN --}}

    <div class="col-md-4"> {{-- START OF 3RD COLUMN --}}
        {{-- START OF MODULE REPETITION --}}
        <div id="lang_weight_1" class="widget-div bg-violet mb-3 min-vh-8">
            <a class="btn col-md-12" data-toggle="modal" data-target="#module_repetition_weights_modal">
                <span class=" hyperspan-col-4">
                    <div class="d-flex align-content-center flex-wrap justify-content-center">
                        <span class="my-3" style="font-size:20px"><strong>Module Repetition Weights</strong></span>
                    </div>
                    <hr>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-4x mb-0 line-h-initial">{{$current_module_repeatition_weights[0]->repeated_times_5}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>Assisted 5x or more before</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$current_module_repeatition_weights[0]->repeated_times_4}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>Assisted 4x before</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$current_module_repeatition_weights[0]->repeated_times_3}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>Assisted 3x before</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$current_module_repeatition_weights[0]->repeated_times_2}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>Assisted 2x before</p>
                        </div>
                    </div>
                    <div class="row mx-1">
                        <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                            <span class="fa-3x mb-0 line-h-initial">{{$current_module_repeatition_weights[0]->repeated_times_1}}</span>
                        </div>
                        <div class="col-md-8 pl-0 d-flex align-content-center flex-wrap justify-content-left">
                            <p>Assisted once before</p>
                        </div>
                    </div>
                </span>
            </a>
        </div>
    </div>  {{-- END OF MODULE REPETITION --}}

</div> {{-- END OF BOARD NO. 1 --}}


{{-- START OF MODALS --}}
{{-- SET NEW ACADEMIC YEAR MODAL --}}
<div class="modal fade" id="update_year_modal" tabindex="-1" role="dialog" aria-labelledby="current_year_modal_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Set Current Semester</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('updateAcademicYear') }}">
                    @csrf
                    <div class="col-md-12">
                        <select name="new_academic_year" id="new_academic_year" class="custom-select" required>
                            <option value="">Choose current semester</option>
                            @foreach ($academicYears as $year)
                            <option value="{{$year->year}}" {{ old('new_academic_year') == $year->year ? 'selected' : '' }}>{{$year->year}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-primary">Set</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> {{-- END OF SET NEW ACADEMIC YEAR MODAL --}}

{{-- PROGRAMMING LANGUAGES WEIGHTS MODAL --}}
<div class="modal fade" id="langauge_weights_modal" tabindex="-1" role="dialog" aria-labelledby="lang_modal_2_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Update weights for similarity in programming languages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 text-right">
                    <a type="button" class="col-md-3 btn btn-sm btn-danger" href="/admin/config/language-weights/reset" onclick="return confirm('Click OK to reset Language Weights to defaults!')">
                        Reset Defaults
                    </a>
                </div>
                <hr>
                <form method="POST" action="{{ route('updateLanguageWeights') }}">
                    @csrf
                    <div class="form-group">
                        <label for="language_weight_1" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="language_weight_1" placeholder="Target language as modules's first choice." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_2" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="language_weight_2" placeholder="Target language as modules's second choice." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_3" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="language_weight_3" placeholder="Target language as modules's third choice." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_4" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="language_weight_4" placeholder="Target language as modules's fourth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_5" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="language_weight_5" placeholder="Target language as modules's fifth choice." required>
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
</div> {{-- END OF PROGRAMMING LANGUAGES WEIGHTS MODAL --}}

{{-- Module Priority Weights MODAL --}}
<div class="modal fade" id="module_priority_weights_modal" tabindex="-1" role="dialog" aria-labelledby="module_priority_weights_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Module Priority Weights</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 text-right">
                    <a type="button" class="col-md-3 btn btn-sm btn-danger" href="/admin/config/module-priority-weights/reset" onclick="return confirm('Click OK to reset Module Priority Weights defaults!')">
                        Reset Defaults
                    </a>
                </div>
                <hr>
                <form method="POST" action="{{ route('updateModulePriorityWeights') }}">
                    @csrf
                    <div class="form-group">
                        <label for="module_priority_weight_1" class="col-form-label">As TA's 1st Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_1" placeholder="Target module as TA's first choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_2" class="col-form-label">As TA's 2nd Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_2" placeholder="Target module as TA's second choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_3" class="col-form-label">As TA's 3rd Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_3" placeholder="Target module as TA's third choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_4" class="col-form-label">As TA's 4th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_4" placeholder="Target module as TA's fourth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_5" class="col-form-label">As TA's 5th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_5" placeholder="Target module as TA's fifth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_6" class="col-form-label">As TA's 6th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_6" placeholder="Target module as TA's sixth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_7" class="col-form-label">As TA's 7th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_7" placeholder="Target module as TA's seventh choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_8" class="col-form-label">As TA's 8th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_8" placeholder="Target module as TA's eighth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_9" class="col-form-label">As TA's 9th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_9" placeholder="Target module as TA's ninth choice." required>
                    </div>
                    <div class="form-group">
                        <label for="module_priority_weight_10" class="col-form-label">As TA's 10th Choice:</label>
                        <input type="number" min="0" class="form-control" name="module_priority_weight_10" placeholder="Target module as TA's tenth choice." required>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-3 text-center">
                            <button type="submit" class="col-md-12 btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> {{-- END OF MODAL 3 --}}

{{-- MODULE REPETITION WEIGHTS MODAL --}}
<div class="modal fade" id="module_repetition_weights_modal" tabindex="-1" role="dialog" aria-labelledby="module_repetition_weights_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">UPDATE WEIGHTS FOR MODULE ASSISTANCE REPETIOTION</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 text-right">
                    <a type="button" class="col-md-3 btn btn-sm btn-danger" href="/admin/config/module-repetition-weights/reset" onclick="return confirm('Click OK to reset Module Repetition Weights to defaults!')">
                        Reset Defaults
                    </a>
                </div>
                <hr>
                <form method="POST" action="{{ route('updateModuleRepetitionWeights') }}">
                    @csrf
                    <div class="form-group">
                        <label for="language_weight_1" class="col-form-label">Assisted more than 4x before.</label>
                        <input type="number" min="0" class="form-control" name="language_weight_1" placeholder="TA assisteed with module more than 4x before." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_2" class="col-form-label">Assisted 4x before.</label>
                        <input type="number" min="0" class="form-control" name="language_weight_2" placeholder="TA assisteed with module 4x before." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_3" class="col-form-label">Assisted 3x before.</label>
                        <input type="number" min="0" class="form-control" name="language_weight_3" placeholder="TA assisteed with module 3x before." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_4" class="col-form-label">Assisted 2x before.</label>
                        <input type="number" min="0" class="form-control" name="language_weight_4" placeholder="TA assisteed with module 2x before." required>
                    </div>
                    <div class="form-group">
                        <label for="language_weight_5" class="col-form-label">Assisted 1x before.</label>
                        <input type="number" min="0" class="form-control" name="language_weight_5" placeholder="TA assisteed with module 1x before." required>
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
</div> {{-- END OF MODULE REPETITIN WEIGHTS MODAL --}}
{{-- END OF MODALS --}}
{{-- </div> --}}
@endsection
