@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Edit TA Preferences') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('updateTAPrefs') }}">
                        @csrf
                        <input type="hidden" value="{{$current_ta_preferences[0]->preference_id}}" name="preference_id">

                        <div class="form-group row">
                            <label for="max_modules" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Modules') }}</label>
                            <div class="col-md-6">
                                <select name="max_modules" id="max_modules" class="custom-select" >
                                    <option value="{{$current_ta_preferences[0]->max_modules }}" >{{$current_ta_preferences[0]->max_modules }} (current)</option>
                                    @for($i=1; $i<= 40; $i++)
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
                                <input type="number" name="max_contact_hours" id="max_contact_hours" class="form-control" value="{{$current_ta_preferences[0]->max_contact_hours }}" placeholder="Weekly total, fractions will be round up!" aria-describedby="helpId" >
                                {{-- <select name="max_contact_hours" id="max_contact_hours" class="custom-select">
                                    <option value="{{$current_ta_preferences[0]->max_contact_hours }}">{{$current_ta_preferences[0]->max_contact_hours }} (current)</option>
                                    @for($i=1; $i<= 40; $i++)
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
                                <input type="number" name="max_marking_hours" id="max_marking_hours" class="form-control" value="{{ $current_ta_preferences[0]->max_marking_hours}}" placeholder="Semester total, fractions will be round up!" aria-describedby="helpId" >
                                {{-- <select name="max_marking_hours" id="max_marking_hours" class="custom-select">
                                    <option value="{{ $current_ta_preferences[0]->max_marking_hours}}">{{ $current_ta_preferences[0]->max_marking_hours}} (current)</option>
                                    @for($i=1; $i<= 40; $i++)
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
                                    <option value="{{ $current_ta_preferences[0]->academic_year }}" >{{ $current_ta_preferences[0]->academic_year }}</option>
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
                            {{-- <label for="have_tier4_visa" class="col-form-label text-md-right">{{ __('I am on a tier 4 visa') }}</label> --}}
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="have_tier4_visa[]" name="have_tier4_visa[]" value="true" {{ $current_ta_preferences[0]->have_tier4_visa== 1 ? 'checked' : '' }} >
                                <label class="form-check-label" for="have_tier4_visa">
                                  {{ __('I am on a tier 4 visa') }}
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
                            <br>
                        </div>


                        @foreach ($current_module_choices as $module1)
                            <div class="form-group row ">
                                <label for="module_{{$module1->priority}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. ' . $module1->priority) }}</label>
                                <div class="col-md-6 mb-md-3">
                                    <select name="module_{{$module1->priority}}_id" id="module_{{$module1->priority}}_id" class="custom-select" required>
                                        <option value="">Choice #{{$module1->priority}}</option>
                                        <option value="{{$module1->module_id}}" selected>{{ $module1->module_name}} (current)</option>
                                        @foreach ($modules as $module)
                                            <option value="{{$module->module_id}}">{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_{{$module1->priority}}_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox" value="true" value="true"
                                        id='done_before_{{$module1->priority}}[]' name="done_before_{{$module1->priority}}[]" {{ $module1->did_before == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="done_before">
                                            <small>I helped with this module before!</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @for($i = count($current_module_choices) + 1; $i<= 10; $i++)
                            <div class="form-group row ">
                                <label for="module_{{$i}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice No. ' . $i) }}</label>
                                <div class="col-md-6">
                                    <select name="module_{{$i}}_id" id="module_{{$i}}_id" class="custom-select">
                                        <option value="">Choice #{{$i}}</option>
                                        @foreach ($modules as $module)
                                            <option value="{{$module->module_id}}">{{$module->module_name}}</option>
                                        @endforeach
                                    </select>
                                    @error('module_{{$module1->priority}}_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox"  value="true" id='done_before_{{$i}}[]' name="done_before_{{$i}}[]" >
                                        <label class="form-check-label" for="done_before">
                                            <small>I helped with this module before!</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endfor

                        {{-- ------------------------
                            PROGRAMMING LANGUAGES
                        ------------------------ --}}

                        <br>
                        <hr>
                        <br>

                        <div class="text-center">
                            <h4>{{ __('Preferred Programming Languages') }}</h4>
                            <br>
                        </div>

                        @for($i = 1; $i <= count($current_language_choices); $i++)
                        <div class="form-group row ">
                            <label for="preferred_language_{{$i}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice No. ') }}{{$i}}</label>
                            <div class="col-md-6">
                                <select name="preferred_language_{{$i}}_id" id="language_{{$i}}_id" class="custom-select">
                                    <option value="">Choice #{{$i}}</option>
                                    <option value="{{$current_language_choices[$i-1]->language_id}}" selected>{{$current_language_choices[$i-1]->language_name}} (current)</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" >{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endfor

                        @for($i = count($current_language_choices) + 1; $i<= 7; $i++)
                            <div class="form-group row ">
                                <label for="preferred_language_{{$i}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice No. ') }}{{$i}}</label>
                                <div class="col-md-6">
                                    <select name="preferred_language_{{$i}}_id" id="language_{{$i}}_id" class="custom-select">
                                        <option value="">Choice #{{$i}}</option>
                                        @foreach ($languages as $language)
                                            <option value="{{$language->language_id}}" >{{$language->language_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endfor

                        <br>
                        <hr>
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Please, click OK to confirm the update!')">
                                    {{ __('Update') }}
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
