@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Show TA Preferences') }}</h4></div>
                <div class="card-body">
                    <form>
                        @csrf

                        <div class="form-group row">
                            <label for="max_modules" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Modules') }}</label>

                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{ $current_ta_preferences[0]->max_modules }}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="max_contact_hours" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Contact Hours') }}</label>

                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$current_ta_preferences[0]->max_contact_hours }}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="max_marking_hours" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Number of Marking Hours') }}</label>

                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{ $current_ta_preferences[0]->max_marking_hours}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="academic_year" class="col-md-4 col-form-label text-md-right">{{ __('Academic Year') }}</label>
                            <div class="col-md-6">
                                {{-- <input id="academic_year" type="text" class="form-control " name="academic_year" value="{{ $current_academic_year->year }}" required> --}}
                                <select name="academic_year" id="academic_year" class="custom-select" disabled>
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
                                <input class="form-check-input" type="checkbox" id="have_tier4_visa[]" name="have_tier4_visa[]" value="true" {{ $current_ta_preferences[0]->have_tier4_visa== 1 ? 'checked' : '' }} disabled>
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
                            <br>
                        </div>


                        @foreach ($current_module_choices as $module)
                            <div class="form-group row ">
                                <label for="module_{{$module->priority}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Module Choice: ' . $module->priority) }}</label>

                                <div class="col-md-6 mb-md-3">
                                    <input type="text" name="" id="module_{{$module->priority}}_id" class="form-control" placeholder="{{ $module->module_name}}" aria-describedby="helpId"  disabled>
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox" value="true"  {{ $module->did_before == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="done_before">
                                            I helped with this module before!
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach

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

                        @foreach ($current_language_choices as $language)
                            <div class="form-group row ">
                                <label for="language_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice: ') }}</label>
                                <div class="col-md-6">
                                    <input type="text" name="" id="module_id" class="form-control" placeholder="{{ $language->language_name}}" aria-describedby="helpId"  disabled>
                                </div>
                            </div>
                        @endforeach

                        <br>
                        <hr>
                        <br>

                    </form>
                    <div class="col-md-6 offset-md-4">
                        <a href="/TA/prefs/edit/{{$current_ta_preferences[0]->preference_id}}" class="btn btn-primary">
                            {{ __('Edit') }}
                        </a>
                        <a href="/TA/prefs/delete/{{$current_ta_preferences[0]->preference_id}}" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                            {{ __('Delete') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
