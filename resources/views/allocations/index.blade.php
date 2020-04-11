@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Add Module Preferences') }}</h4></div>
                <div class="card-body">
                    <ul><strong>Allocation related data dashboard</strong>
                        <li>Data will be viewed as button cards,</li>
                        <li>Which, onclick, will show modals,</li>
                        <li>Which will update the database!</li>
                        <li>AHA!;)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
