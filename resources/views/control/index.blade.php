@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Module Preferences') }}</h4></div>
                <div class="card-body">
                    <strong>Admin home page!</strong><br>
                    Will show data about allocations<br>
                    <ul>
                        <li>numbers of modules with preferences</li>
                        <li>numbers of TA with preferences</li>
                        <li>And other data, which I'll add once I think of it!</li>
                    </ul>
                    <br>
                    <br>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
