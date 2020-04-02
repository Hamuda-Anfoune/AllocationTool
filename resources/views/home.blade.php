@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('account_type_id') == 002)
                        {{--
                            Show the modules with preferences
                            and then the ones without them.
                        --}}
                        <h3><strong>Modules You Teach</strong></h3>
                        @if(($preferenced_convenor_modules->count()) > 0)
                            <h5><i>Modules With Preferences</i>:</h5>
                            <ul>
                                @foreach($preferenced_convenor_modules as $module)
                                    <li>{{$module->module_id}} - {{$module->academic_year}}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if(($nonpreferenced_convenor_modules->count()) > 0)
                            <h5><i>Modules Without Preferences</i></h5>
                            <ul>
                                @foreach($nonpreferenced_convenor_modules as $module)
                                    <li>{{$module->module_name}}</li>
                                @endforeach
                            </ul>
                        @endif

                    @endif
                    @if (session('account_type_id') == 003 || session('account_type_id') == 004)
                        <h3>Your Preferences</h3>
                        @if(($TAs_preferences->count()) > 0)
                            <ul>
                                @foreach($TAs_preferences as $preference)
                                    <li>{{$preference->academic_year}}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
