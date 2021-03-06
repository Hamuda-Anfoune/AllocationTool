@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center">
                    <a class="btn btn-info btn-sm" data-toggle="collapse" href="#instructions" role="button" aria-expanded="false" aria-controls="instructions">
                        University Users
                    </a>
                </div>
                <div class="card-body">
                    <div class="collapse" id="instructions">
                        <div class="mb-3 text-center">
                            <i class="fab fa-balance-scale fa-2x fa-fw"></i>
                        </div>
                        <ul>
                            <li>You can add new university users from here.</li>
                            <li>Only users you add can register to use this tool.</li>
                            <li>Adding user emails here does not mean they can directly use the tool.</li>
                            <li>They need to register to be able to use it.</li>
                            <li>This serves as a university database and prevents unauthorised usage of the tool.</li>
                        </ul>
                        <br>
                    </div>
                </div>
            </div>

            <hr>

            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Add New University User') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storeUniversityUser') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('User Email') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="account_type_id" class="col-md-4 col-form-label text-md-right">{{ __('Account Type') }}</label>

                            <div class="col-md-6">
                                <select name="account_type_id" id="account_type_id" class="custom-select" required>
                                    <option value="">Choose a type</option>
                                    @foreach ($account_types_without_super as $type)
                                        <option value="{{$type->account_type_id}}" {{ old('account_type_id') == $type->account_type_id ? 'selected' : '' }}>{{$type->account_type}}</option>
                                    @endforeach
                                </select>
                                @error('account_type_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary col-md-2">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
