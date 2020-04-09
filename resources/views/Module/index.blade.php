@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Module Preferences') }}</h4></div>
                <div class="card-body">
                    {{--
                            Show the modules with preferences
                            and then the ones without them.
                        --}}
                        <h3><strong>Modules You Teach</strong></h3>
                        {{-- @if(($preferenced_convenor_modules->count()) > 0) --}}
                        @if(sizeof($preferenced_convenor_modules) > 0)
                            <h5><i>Modules With Preferences In The Current Year</i></h5>
                            <ul>
                                @foreach($preferenced_convenor_modules as $module)
                                    <li>{{$module->module_id}} - {{$module->module_name}}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if(($nonpreferenced_convenor_modules->count()) > 0)
                            <h5><i>Modules Without Preferences</i></h5>
                            <ul>
                                @foreach($nonpreferenced_convenor_modules as $module)
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
