@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center">
                    <h4>{{ __('All Registered Teaching Assistants Without Preferences This Semester') }}</h4>
                </div>
                <div class="card-body">
                    @if(sizeof($tas_without_prefs) > 0)
                        @foreach($tas_without_prefs as $user)
                            <div class="row">
                                <div class="col-md-9">
                                    <ul>
                                        <li>{{$user->name}}&emsp;
                                            <ul>
                                                <li>University ID: {{$user->email}}</li>
                                                <li>TA Type: {{$user->account_type}}</li>
                                                <li>Registered At: {{$user->created_at}}</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-3 text-center">
                                    <a class="btn btn-sm btn-danger" onclick="return confirm('You are trying to delete {{$user->name}}. Press OK to continue?')" href="/admin/delete/user/{{$user->email}}">Delete</a>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @endif
                    <div class="text-right">
                        <a class="btn btn-sm btn-primary" href="/admin/university-users/add">Add New University User</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

