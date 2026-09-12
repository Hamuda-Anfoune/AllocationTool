@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}

{{Session::get('max_modules')}}
{{-- {{Session::get('semester')}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Teaching Assistants\' Preferences Supmission') }}</h4></div>
                <div class="card-body align-items-center">
                    <div class="text-center">
                        <i class="fa fa-2x fa-500px" aria-hidden="true"></i><br>
                        <strong>Before submitting your preferences!</strong> <br> You can check the modules' prefereces <a href="/modules/prefs/all">here.</a>
                    </div>
                    <hr>

                    <form method="POST" action="{{ route('storeTAPrefs') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="max_modules" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Modules') }}</label>

                            <div class="col-md-6">
                                {{-- <input id="max_modules" type="max_modules" class="form-control @error('max_modules') is-invalid @enderror" name="max_modules" value="{{ old('max_modules') }}" required autocomplete="max_modules"> --}}
                                <select name="max_modules" id="max_modules" class="custom-select" >
                                    @for($i=1; $i<= 20; $i++)
                                        <option value="{{$i}}" {{ old('max_modules') == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                </select>
                                @error('max_modules')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="max_contact_hours" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Contact Hours Per Week') }}</label>

                            <div class="col-md-6">
                                <input type="number" name="max_contact_hours" id="max_contact_hours" class="form-control" value="{{ old('max_contact_hours') }}" placeholder="Weekly total, fractions will be round up!" aria-describedby="helpId" >
                                {{-- <select name="max_contact_hours" id="max_contact_hours" class="custom-select">
                                    @for($i=0; $i<= 50; $i++)
                                        <option value="{{$i}}" {{ old('max_contact_hours') == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                </select> --}}
                                @error('max_contact_hours')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="max_marking_hours" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Marking Hours Per Semester') }}</label>
                            <div class="col-md-6">
                                <input type="number" min="7" name="max_marking_hours" id="max_marking_hours" class="form-control" value="{{ old('max_marking_hours') }}" placeholder="Semester total, fractions will be round up!" aria-describedby="helpId" >
                                {{-- <select name="max_marking_hours" id="max_marking_hours" class="custom-select">
                                    @for($i=0; $i<= 50; $i++)
                                        <option value="{{$i}}" {{ old('max_marking_hours') == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                </select> --}}
                                @error('max_marking_hours')
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
                                    <option value="{{ $current_academic_year->year }}" {{ old('academic_year') == $i ? 'selected' : '' }}>{{ $current_academic_year->year }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- ------------------------
                            VISA STATUS
                        ------------------------ --}}

                        <br>
                        <hr>
                        <br>
                        <div class="text-center">
                            <h4>{{ __('Visa Status') }}</h4>
                        </div>

                        <br>

                        <div class="form-group row justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="have_tier4_visa[]" name="have_tier4_visa[]" value="true" {{ (is_array(old('have_tier4_visa')) && in_array(true, old('have_tier4_visa'))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="have_tier4_visa">
                                  I am on a tier 4 visa
                                </label>
                            </div>
                        </div>

                        <br>
                        <hr>
                        <br>

                        {{-- ------------------------
                            MODULE CHOICES
                        ------------------------ --}}

                        <div class="text-center">
                            <h4>{{ __('Module Choices') }}</h4>
                            <p>
                                <small>
                                    Choose up to ten modules with which you would like to assist this semester.
                                </small>
                            </p>
                            <br>
                        </div>

                        <div class="form-group row ">
                            <label for="module_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 1') }}</label>

                            <div class="col-md-3">
                                <select name="module_1_id" id="module_1_id" class="custom-select" required>
                                    <option value="">Choice #1</option>
                                    @foreach ($modules as $module)
                                        <option value="{{$module->module_id}}" {{ old('module_1_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                    @endforeach
                                </select>
                                @error('module_1_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="checkbox" value="true" id="done_before_1[]" name="done_before_1[]" {{ (is_array(old('done_before_1')) && in_array(true, old('done_before_1'))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="done_before_1">
                                  I helped with this module before!
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module_2_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 2') }}</label>
                            <div class="col-md-3">
                                <select name="module_2_id" id="module_2_id" class="custom-select">
                                    <option value="">Choice #2</option>
                                    @foreach ($modules as $module)
                                        <option value='{{$module->module_id}}' {{ old('module_2_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                    @endforeach
                                </select>
                                @error('module_2_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="checkbox" value="true" id="done_before_2[]" name="done_before_2[]" {{ (is_array(old('done_before_2')) && in_array(true, old('done_before_2'))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="done_before_2">
                                  I helped with this module before!
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module_3_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 3') }}</label>
                            <div class="col-md-3">
                                <select name="module_3_id" id="module_3_id" class="custom-select" >
                                    <option value="">Choice #3</option>
                                    @foreach ($modules as $module)
                                        <option value='{{$module->module_id}}' {{ old('module_3_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                    @endforeach
                                </select>
                                @error('module_3_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="checkbox" value="true" id="done_before_3[]" name="done_before_3[]" {{ (is_array(old('done_before_3')) && in_array(true, old('done_before_3'))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="done_before_3">
                                  I helped with this module before!
                                </label>
                            </div>
                        </div>

                        <div id="module_4_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_4_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 4') }}</label>
                                <div class="col-md-3">
                                    <select name="module_4_id" id="module_4_id" class="custom-select">
                                        <option value="">Choice #4</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_4_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_4_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_4[]" name="done_before_4[]" {{ (is_array(old('done_before_4')) && in_array(true, old('done_before_4'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_4">
                                      I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="module_5_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_5_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 5') }}</label>
                                <div class="col-md-3">
                                    <select name="module_5_id" id="module_5_id" class="custom-select">
                                        <option value="">Choice #5</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_5_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_5_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_5[]" name="done_before_5[]" {{ (is_array(old('done_before_5')) && in_array(true, old('done_before_5'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_5">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="module_6_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_6_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 6') }}</label>
                                <div class="col-md-3">
                                    <select name="module_6_id" id="module_6_id" class="custom-select">
                                        <option value="">Choice #6</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_6_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_6_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_6[]" name="done_before_6[]" {{ (is_array(old('done_before_6')) && in_array(true, old('done_before_6'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_6">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="module_7_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_7_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 7') }}</label>
                                <div class="col-md-3">
                                    <select name="module_7_id" id="module_7_id" class="custom-select">
                                        <option value="">Choice #7</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_7_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_7_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_7[]" name="done_before_7[]" {{ (is_array(old('done_before_7')) && in_array(true, old('done_before_7'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_7">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="module_8_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_8_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 8') }}</label>
                                <div class="col-md-3">
                                    <select name="module_8_id" id="module_8_id" class="custom-select">
                                        <option value="">Choice #8</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_8_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_8_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_8[]" name="done_before_8[]" {{ (is_array(old('done_before_8')) && in_array(true, old('done_before_8'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_8">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="module_9_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_9_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 9') }}</label>
                                <div class="col-md-3">
                                    <select name="module_9_id" id="module_9_id" class="custom-select">
                                        <option value="">Choice #9</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_9_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_9_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_9[]" name="done_before_9[]" {{ (is_array(old('done_before_9')) && in_array(true, old('done_before_9'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_9">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="module_10_div" style="display:none">
                            <div class="form-group row">
                                <label for="module_10_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. 10') }}</label>
                                <div class="col-md-3">
                                    <select name="module_10_id" id="module_10_id" class="custom-select">
                                        <option value="">Choice #10</option>
                                        @foreach ($modules as $module)
                                            <option value='{{$module->module_id}}' {{ old('module_10_id') == $module->module_id ? 'selected' : '' }}>{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_10_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check ml-3">
                                    <input class="form-check-input" type="checkbox" value="true" id="done_before_10[]" name="done_before_10[]" {{ (is_array(old('done_before_10')) && in_array(true, old('done_before_10'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="done_before_10">
                                    I helped with this module before!
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 offset-4 col-md-8 text-left">
                            <a class="btn btn-sm btn-primary" type="button" onclick="showModuleDivs()" id="mod_btn">Add Another Module</a>
                        </div>

                        {{-- ------------------------
                            PROGRAMMING LANGUAGES
                        ------------------------ --}}

                        <br>
                        <hr>
                        <br>

                        <div class="text-center">
                            <h4>{{ __('Preferred Programming Languages') }}</h4>
                            <p>
                                <small>
                                    Choose up to seven programming languages which you are capable to use.<br>
                                    Your choices will be used to consider how well you are fit to assist with each module.
                                </small>
                            </p>
                            <br>
                        </div>

                        <div class="form-group row ">
                            <label for="preferred_language_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 1') }}</label>

                            <div class="col-md-6">
                                <select name="preferred_language_1_id" id="preferred_language_1_id" class="custom-select">
                                    <option value="">Choice #1</option>
                                    @foreach ($languages as $language)
                                        <option value='{{$language->language_id}}' {{ old('preferred_language_1_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('preferred_language_1_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="preferred_language_2_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 2') }}</label>

                            <div class="col-md-6">
                                <select name="preferred_language_2_id" id="preferred_language_2_id" class="custom-select">
                                    <option value="">Choice #2</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ old('preferred_language_2_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('preferred_language_2_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="preferred_language_3_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 3') }}</label>

                            <div class="col-md-6">
                                <select name="preferred_language_3_id" id="preferred_language_3_id" class="custom-select">
                                    <option value="">Choice #3</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ old('preferred_language_3_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                                @error('preferred_language_3_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div id="language_4_div" style="display:none">
                            <div class="form-group row ">
                                <label for="preferred_language_4_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 4') }}</label>
                                <div class="col-md-6">
                                    <select name="preferred_language_4_id" id="preferred_language_4_id" class="custom-select">
                                        <option value="">Choice #4</option>
                                        @foreach ($languages as $language)
                                            <option value="{{$language->language_id}}" {{ old('preferred_language_4_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_language_4_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="language_5_div" style="display:none">
                            <div class="form-group row ">
                                <label for="preferred_language_5_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 5') }}</label>

                                <div class="col-md-6">
                                    <select name="preferred_language_5_id" id="preferred_language_5_id" class="custom-select">
                                        <option value="">Choice #5</option>
                                        @foreach ($languages as $language)
                                            <option value="{{$language->language_id}}" {{ old('preferred_language_5_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_language_5_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="language_6_div" style="display:none">
                            <div class="form-group row ">
                                <label for="preferred_language_6_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 6') }}</label>

                                <div class="col-md-6">
                                    <select name="preferred_language_6_id" id="preferred_language_6_id" class="custom-select">
                                        <option value="">Choice #6</option>
                                        @foreach ($languages as $language)
                                            <option value="{{$language->language_id}}" {{ old('preferred_language_6_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_language_6_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="language_7_div" style="display:none">
                            <div class="form-group row ">
                                <label for="preferred_language_7_id" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Language No. 7') }}</label>

                                <div class="col-md-6">
                                    <select name="preferred_language_7_id" id="preferred_language_7_id" class="custom-select">
                                        <option value="">Choice #7</option>
                                        @foreach ($languages as $language)
                                            <option value="{{$language->language_id}}" {{ old('preferred_language_7_id') == $language->language_name ? 'selected' : '' }}>{{$language->language_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_language_7_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 offset-4 col-md-8 text-left">
                            <a class="btn btn-sm btn-primary" type="button" onclick="showLangDivs('lang_btn')" id="lang_btn">Add Another Language</a>
                        </div>

                        <br>
                        <hr>
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success col-md-8">
                                    {{ __('Submit Preference') }}
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

<script>

</script>
