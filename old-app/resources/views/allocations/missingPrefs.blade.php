@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                Cannot initiate allocation before all TAs and module convenors submit thier preferences!
            </div>
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Missing Preferences') }}</h4></div>
                <div class="card-body">
                    {{--
                            Show the TAs and modules without preferences
                        --}}

                        @if(sizeof($tas_without_prefs) > 0)
                        <h6><strong>Teaching Assistants Without Preferences</strong></h6>
                            <p>The following teaching assistants have not submitted preferences for the current academic year:</p>
                            <ul>
                                @foreach($tas_without_prefs as $ta)
                                    <li>{{$ta->name}} - {{$ta->email}}</li>
                                @endforeach
                            </ul>
                        @endif

                        <hr>

                        @if(sizeof($modules_without_prefs) > 0)
                        <h6><strong>Modules Without Preferences</strong></h6>
                            <p>The following modules have not submitted preferences for the current academic year:</p>
                            <ul>
                                @foreach($modules_without_prefs as $module)
                                    <li>{{$module->module_id}} - {{$module->module_name}}</li>
                                @endforeach
                            </ul>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
