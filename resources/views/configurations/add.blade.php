@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Module Preferences') }}</h4></div>
                <div class="card-body">
                    <strong>TODO:</strong>
                    - Add the control data for the admin:<br>
                    \tweight of done before<br>
                    \tweight of each module match<br>
                    - Choice to calculate
                    \tOther configurations<br>
                    <hr>
                    <br>
                    <br>

                    <form method="POST" action="{{ route('storeConfigs') }}">
                        @csrf
                        <p><strong>The weights below will be used in calculating the Rank Order List (ROL) of teaching assistants for each module:</strong></p>
                        <div class="form-group row">
                            <label for="done_before" class="col-md-4 col-form-label text-md-right">{{ __('Weight of assisting with the the module before:') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="done_before" id="done_before" placeholder="Weight of assisting with the the module before">
                                @error('done_before')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="language_1" class="col-md-4 col-form-label text-md-right">{{ __('Weight of assisting with the the module before:') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="language_1" id="language_1" placeholder="Weight of assisting with the the module before">
                                @error('language_1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Number of Contact Hours') }}</label>

                            <div class="col-md-6">
                                <select name="no_of_contact_hours" id="no_of_contact_hours" class="custom-select">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                                @error('no_of_contact_hours')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_of_marking_hours" class="col-md-4 col-form-label text-md-right">{{ __('Number of Marking Hours') }}</label>

                            <div class="col-md-6">
                                <select name="no_of_marking_hours" id="no_of_marking_hours" class="custom-select">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                                @error('no_of_marking_hours')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="academic_year" class="col-md-4 col-form-label text-md-right">{{ __('Academic Year') }}</label>
                            <div class="col-md-6">
                                {{-- <input id="academic_year" type="text" class="form-control " name="academic_year" value="{{ $current_academic_year->year }}" required> --}}
                                <select name="academic_year" id="academic_year" class="custom-select">
                                    <option value="{{ $current_academic_year->year }}">{{ $current_academic_year->year }}</option>
                                </select>
                                {{-- @error('academic_year')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror --}}
                            </div>
                        </div>

                        <br>
                        <hr>
                        <br>

                        {{-- ------------------------
                            PROGRAMMING LANGUAGES
                        ------------------------ --}}

                        <div class="text-center">
                            <h4>{{ __('Programming Languages') }}</h4>
                            <br>
                        </div>

                        <div class="form-group row ">
                            <label for="language_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Used Language No. 1') }}</label>

                            <div class="col-md-6">
                                <select name="language_1_id" id="language_1_id" class="custom-select">
                                    <option value="">Choice #1</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ old('language_1_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('language_1_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="language_2_id" class="col-md-4 col-form-label text-md-right">{{ __('Used Language No. 1') }}</label>

                            <div class="col-md-6">
                                <select name="language_2_id" id="language_2_id" class="custom-select">
                                    <option value="">Choice #2</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ old('language_2_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('language_2_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="language_3_id" class="col-md-4 col-form-label text-md-right">{{ __('Used Language No. 3') }}</label>

                            <div class="col-md-6">
                                <select name="language_3_id" id="language_3_id" class="custom-select">
                                    <option value="">Choice #3</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ old('language_3_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('language_3_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <br>
                        <hr>
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Add') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
