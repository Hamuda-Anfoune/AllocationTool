@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('All Registered Teaching Assistants') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($modules_without_prefs) > 0)
                        @foreach($modules_without_prefs as $module)
                            <div class="row">
                                <div class="col-md-12">
                                    <ul>
                                        <li>{{$module->module_name}}&emsp;
                                            <ul>
                                                <li>University ID: {{$module->convenor_email}}</li>
                                                <li>TA Type: {{$module->convenor_name}}</li>
                                                <li>Registered At: {{$module->module_id}}</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @endif
                    <div class="text-right">
                        <a class="btn btn-sm btn-primary" href="/admin/modules/add">Add New Module</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

