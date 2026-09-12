@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center">
                    <a class="btn btn-info btn-sm" data-toggle="collapse" href="#instructions" role="button" aria-expanded="false" aria-controls="instructions">Instructions</a>
                </div>
                <div class="card-body">
                    <div class="collapse" id="instructions">
                        <div class=" mb-3 text-center">
                            <i class="fab fa-balance-scale fa-2x fa-fw"></i>
                        </div>
                        <ul>
                            <li>University users are users registered in the university database, but not necessarily registered as users in the allocation system!</li>
                            <li>However, emails used to register in the allocation system have to exist in the universtiy users table.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr>

            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('All University Users') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($university_users) > 0)
                        {{-- <h6>{{ __('Users') }}</h6> --}}
                        @foreach($university_users as $user)
                            <ul>
                                <li><a href="#">University ID: {{$user->email}}</a>
                                    <ul>
                                        <li>Account Type: {{$user->account_type}}</li>
                                        <li>Registered At: {{$user->created_at}}</li>
                                    </ul>
                                </li>
                            </ul>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
