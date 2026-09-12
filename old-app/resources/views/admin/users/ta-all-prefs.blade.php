@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('All Registered Teaching Assistants') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($TAs_preferences) > 0)
                        @foreach($TAs_preferences as $pref)
                            <div class="row">
                                <div class="col-md-9">
                                    <ul>
                                        <li>
                                            <a href="/TA/prefs/show/{{$pref->preference_id}}">{{$pref->preference_id}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <div class="col-md-12 text-center mb-3">
                                        <a class="col-md-12 btn btn-sm btn-danger" onclick="return confirm('You are trying to delete the submission. Press OK to continue?')" href="/TA/prefs/delete/{{$pref->preference_id}}">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @endif
                    {{-- <div class="text-right">
                        <a class="btn btn-sm btn-primary" href="/admin/university-users/add">Add New University User</a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
