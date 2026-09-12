@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Your Preferences Per Semester') }}</h4></div>
                <div class="card-body">
                    @if(($TAs_preferences->count()) <= 0)
                        <div class="text-right">
                            <a class="btn btn-outline-primary" href="/TA/prefs/add">Submit Preferences</a>
                        </div>
                    @endif
                    @if(($TAs_preferences->count()) > 0)
                    <h5>Previous Preferences</h5>
                    <ul>
                            @foreach($TAs_preferences as $preference)
                                <li><a href="/TA/prefs/show/{{$preference->preference_id}}">{{$preference->academic_year}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
