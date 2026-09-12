@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Add a New Module') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storeModule') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="module_name" class="col-md-4 col-form-label text-md-right">{{ __('Module Name') }}</label>
                            <div class="col-md-6">
                                <input id="module_name" type="text" class="form-control @error('module_name') is-invalid @enderror" name="module_name" value="{{ old('module_name') }}" required autocomplete="module_name" autofocus>
                                @error('module_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module_id" class="col-md-4 col-form-label text-md-right">{{ __('Module ID') }}</label>
                            <div class="col-md-6">
                                <input id="module_id" type="text" class="form-control @error('module_id') is-invalid @enderror" name="module_id" value="{{ old('module_id') }}" required autocomplete="text" autofocus>
                                @error('module_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="academic_year" class="col-md-4 col-form-label text-md-right">{{ __('Academic Year') }}</label>
                            <div class="col-md-6">
                                <select name="academic_year" id="academic_year" class="custom-select">
                                    <option value="{{ $current_academic_year }}">{{ $current_academic_year }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="convenor_email" class="col-md-4 col-form-label text-md-right">{{ __('Convenor Name') }}</label>

                            <div class="col-md-6">
                                <select name="convenor_email" id="convenor_email" class="custom-select" required>
                                    <option value="">Choose a convenor</option>
                                    @foreach ($convenors as $convenor)
                                        <option value="{{$convenor->email}}" {{ old('convenor_email') == $convenor->email ? 'selected' : '' }}>{{$convenor->name}}</option>
                                    @endforeach
                                </select>
                                @error('convenor_email')
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
