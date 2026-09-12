@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Preferences of Modules You Teach') }}</h4></div>
                <div class="card-body">
                    <h5>Modules With Preferences In The Current Year</h5>
                    @if(sizeof($preferenced_convenor_modules) > 0)
                        <ul>
                            @foreach($preferenced_convenor_modules as $module)
                                <li><a href="/modules/prefs/show/{{$module->module_id}}/{{$current_academic_year}}">{{$module->module_id}} - {{$module->module_name}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                    <hr>
                    <h5>Modules Without Preferences In The Current Year</h5>
                    @if(($nonpreferenced_convenor_modules->count()) > 0)
                        <ul>
                            @foreach($nonpreferenced_convenor_modules as $module)
                                <li><a href="/module/prefs/add">{{$module->module_id}} - {{$module->module_name}}</a></li>
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
