@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center">
                    <a class="btn btn-info btn-sm" data-toggle="collapse" href="#instructions" role="button" aria-expanded="false" aria-controls="instructions">Instructions</a>
                </div>
                <div class="card-body">
                    <div class="collapse" id="instructions">
                        <i class="fab fa-balance-scale fa-sm fa-fw"></i>
                        <strong>Admin home page!</strong><br>
                        <ul>
                            <li>Shows data and stats regarding the users and allocations.</li>
                            <li>Data shows in button cards</li>
                            <li>Once clicked, button cards work as links or models.</li>
                            <li>Modals are used to update database</li>
                        </ul>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row justify-content-center"> {{-- START OF ALLOCATION CARD --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4>{{ __('Allocation') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-6 text-center">
                            <a class=" col-md-10 btn btn-success" href="{{ route('allocate') }}" onclick="return confirm('Press OK to continue?')">
                                {{ __('Allocate Current Semester') }}
                            </a>
                        </div>
                        <div class="col-md-6 text-center">
                            <a class=" col-md-10 btn btn-danger" href="{{ route('deleteCurrentAllocation') }}" onclick="return confirm('Are you sure?')">
                                {{ __('Clear Current Allocation') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- END OF ALLOCATION CARD --}}

    <hr>

    <div class="row md-col-12"> {{-- START OF BOARD NO. 1 --}}
        <div class="col-md-3">
            <div id="langs" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-cubes fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Miscellaneous</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" data-toggle="modal" data-target="#update_year_modal" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Active Admins</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['active_admins_count'] }}</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" href="/admin/show/all-registered-users" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">All Active Users</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['active_users_count'] }}</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-red mb-3 min-vh-8">
                <a class="btn col-md-12" href="/admin/university-users" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">All University Users</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['university_users_count'] }}</span>
                    </span>
                </a>
            </div>
        </div>    {{-- END OF 1ST COLUMN --}}

        <div class="col-md-3">    {{-- START OF 2ND COLUMN --}}
            <div id="done-before-weight-head" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-user fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Module Convenors</p>
                </div>
            </div>
            <div id="no-of-convenors" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="/admin/university-users/add">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Active Module Convenors</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['active_convenor_count'] }}</span>
                    </span>
                </a>
            </div>
            <div id="lang-weight-3" class="widget-div bg-skyblue mb-3 min-vh-8">
                <a class="btn col-md-12" href="/admin/university-users/add">
                    <span class=" hyperspan">
                        <p class="widget-title">Covenors Missing Prefs</p>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['active_convenors_missing_prefs_count'] }}</span>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 2ND COLUMN --}}

        <div class="col-md-3">  {{-- START OF 3RD COLUMN --}}
            <div id="lang-weight-primary" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-users fa-2x"></span>
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
                    <span class="fa-4x mb-0 line-h-initial">{{ $data['active_tas_count'] }}</span>
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
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['active_tas_without_prefs_count'] }}</span>
                    </span>
                </a>
            </div>
        </div>  {{-- END OF 3RD COLUMN --}}

        <div class="col-md-3">  {{-- START OF 4TH COLUMN --}}
            <div id="lang-weight-primary" class="row widget-div widget-primary bg-white text-center p-2 mb-3 ml-0 mr-0">
                <div class="col-md-4 d-flex align-content-center flex-wrap justify-content-center">
                    <span class="fas fa-book fa-2x"></span>
                </div>
                <div class="col-md-8 d-flex align-content-center flex-wrap justify-content-center">
                    <p class="widget-title">Modules</p>
                </div>
            </div>
            <div id="no-of-tas" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="/admin/modules/add">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Taught Modules</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['current_year_modules_count'] }}</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-orange mb-3 min-vh-8">
                <a class="btn col-md-12" href="/modules/prefs/all">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Modules Without Prefs</p>
                        </div>
                        <hr>
                        <span class="fa-4x mb-0 line-h-initial">{{ $data['current_modules_without_prefs_count'] }}</span>
                    </span>
                </a>
            </div>
            <div id="no-of-tas" class="widget-div bg-violet mb-3 min-vh-8">
                <a class="btn col-md-12" data-toggle="modal" data-target="#update_year_modal" data-whatever="@getbootstrap">
                    <span class=" hyperspan">
                        <div class="d-flex align-content-center flex-wrap justify-content-center">
                            <p class="widget-title">Current Academic Year</p>
                        </div>
                        <hr>
                        <span class="fa-2x mb-0 line-h-initial">{{ $data['current_academic_year'] }}</span>
                    </span>
                </a>
            </div>
        </div> {{-- END OF 4TH COLUMN --}}
    </div> {{-- END OF BOARD NO. 1 --}}


    {{-- START OF MODALS --}}
    {{-- SET NEW ACADEMIC YEAR MODAL --}}
    <div class="modal fade" id="update_year_modal" tabindex="-1" role="dialog" aria-labelledby="current_year_modal_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Set Academic Year</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('updateAcademicYear') }}">
                        @csrf
                        <div class="col-md-12">
                            <select name="new_academic_year" id="new_academic_year" class="custom-select" required>
                                <option value="">Choose new academic year</option>
                                @foreach ($data['academicYears'] as $year)
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
