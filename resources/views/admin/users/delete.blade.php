@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('All Registered Users') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($all_users) > 0)
                        {{-- <h6>{{ __('Users') }}</h6> --}}
                        @foreach($all_users as $user)
                            <ul>
                                <li><a href="#">{{$user->name}}</a>
                                    <ul>
                                        <li>University ID: {{$user->email}}</li>
                                        <li>Account Type: {{$user->account_type}}</li>
                                        <li>Account Active? {{$user->active}}</li>
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
