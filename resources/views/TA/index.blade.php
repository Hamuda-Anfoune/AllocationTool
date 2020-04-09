@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Add Module Preferences') }}</h4></div>
                <div class="card-body">
                    <h3>Your Preferences</h3>
                    @if(($TAs_preferences->count()) > 0)
                        <ul>
                            @foreach($TAs_preferences as $preference)
                                <li>{{$preference->academic_year}}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
