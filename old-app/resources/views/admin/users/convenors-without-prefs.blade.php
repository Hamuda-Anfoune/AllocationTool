@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center">
                    <h4>{{ __('Module Convenors Without Preferences This Seester') }}</h4>
                </div>
                <div class="card-body">
                    @if(sizeof($Convenors_without_prefs_with_modules) > 0)
                        @foreach($Convenors_without_prefs_with_modules as $user)
                            <div class="row">
                                <div class="col-md-9">
                                    <ul>
                                        <li>{{$user->name}}&emsp;
                                            <ul>
                                                <li>University ID: {{$user->convenor_email}}</li>
                                                <li>Modules</li>
                                                @if(sizeof($user->modules)>0)
                                                    <ul>
                                                        @foreach ($user->modules as $module)
                                                            <li>{{$module->module_id}} - {{$module->module_name}}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                {{-- <div class="col-md-3 text-center">
                                    <a class="btn btn-sm btn-danger" onclick="return confirm('You are trying to delete {{$user->convenor_email}}. Press OK to continue?')" href="/admin/delete/user/{{$user->email}}">Delete</a>
                                </div> --}}
                            </div>
                            <hr>
                        @endforeach
                    @endif
                    <div class=" col-md-12 row text-right">
                        <div class="col-md-4 m-2">
                            <a class="col-md-12 btn btn-sm btn-primary" href="/modules/prefs/all">All Module Preferences</a>
                        </div>
                        <div class="col-md-4 m-2">
                            <a class="col-md-12 btn btn-sm btn-primary" href="/admin/modules/add">Add New Module</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

