@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('All Modules\' Preferences Based on Academic Years') }}</h4></div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fab fa-balance-scale fa-2x fa-fw"></i><br>
                        <small>Choose an academic year to show the module preferences.</small>
                    </div>
                    <form method="post" action="{{ route('showAllModulePrefs') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-primary" type="submit">View Modules</button>
                            </div>
                            <select class="custom-select" id="academic_year" name="academic_year">
                                <option value="{{$academic_year}}" selected>{{$academic_year}}</option>
                                @foreach ($academic_years as $year)
                                    <option value="{{$year->year}}">{{$year->year}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <hr>
                    <div class="my-4 col-md-12 text-right">
                        <a class="btn btn-sm btn-primary" href="/admin/modules/add">Add New modules</a>
                    </div>
                    <hr>
                    <h5>Modules With Preferences for {{$academic_year}}</h5>
                    @if(($modules_with_prefs->count()) > 0)
                        <ul>
                            @foreach ($modules_with_prefs as $module)
                                <li><a href="/modules/prefs/show/{{$module->module_id}}/{{$academic_year}}">{{$module->module_id}} - {{$module->module_name}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                    <br>
                    <hr>
                    <h5>Modules Without Preferences for {{$academic_year}}</h5>
                    @if(($modules_without_prefs->count()) > 0)
                        <ul>
                            @foreach ($modules_without_prefs as $module)
                                <li>{{$module->module_id}} - {{$module->module_name}}</li>
                            @endforeach
                        </ul>
                    @endif
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
