@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Update Module Preferences') }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('updateModulePrefs') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Module') }}</label>

                            <div class="col-md-6">
                                <select name="module_id" id="module_id" class="custom-select" required>
                                    <option value="{{$module_basic_prefs[0]->module_id}}">{{$module_basic_prefs[0]->module_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_of_assistants" class="col-md-4 col-form-label text-md-right">{{ __('Number of Assistants Required') }}</label>

                            <div class="col-md-6">
                                <select name="no_of_assistants" id="no_of_assistants" class="custom-select">
                                    <option value="{{$module_basic_prefs[0]->no_of_assistants}}">{{$module_basic_prefs[0]->no_of_assistants}} (current)</option>
                                    @for($i=1; $i<= 10; $i++)
                                        <option value="{{$i}}" {{ $module_basic_prefs[0]->no_of_assistants == $i ? 'selected' : '' }}>{{$i}}</option>
                                    @endfor
                                </select>

                                @error('no_of_assistants')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contact Hours Per Week') }}</label>

                            <div class="col-md-6">
                                <input type="number" name="no_of_contact_hours" id="no_of_contact_hours" class="form-control" value="{{$module_basic_prefs[0]->no_of_contact_hours}}" placeholder="Weekly total, fractions will be round up!" aria-describedby="helpId" >
                                @error('no_of_contact_hours')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_of_marking_hours" class="col-md-4 col-form-label text-md-right">{{ __('Total Marking Hours Per Semester') }}</label>

                            <div class="col-md-6">
                                <input type="number" name="no_of_marking_hours" id="no_of_marking_hours" class="form-control" value="{{$module_basic_prefs[0]->no_of_marking_hours}}" placeholder="Semester total, fractions will be round up!" aria-describedby="helpId" >
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
                                <select name="academic_year" id="academic_year" class="custom-select">
                                    <option value="{{ $current_academic_year }}">{{ $current_academic_year }}</option>
                                </select>
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

                        @foreach ($module_language_choices as $language1)
                        <div class="form-group row ">
                            <label for="language_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice No. ') }}{{$language1->priority}}</label>
                            <div class="col-md-6">
                                <select name="language_{{$language1->priority}}_id" id="language_7_id" class="custom-select">
                                    <option value="">Neglect this choice</option>
                                    <option value="{{$language1->language_id}}">{{$language1->Language_name}} (current)</option>
                                    @foreach ($languages as $language)
                                        <option value="{{$language->language_id}}" {{ $language1->language_id == $language->language_id ? 'selected' : '' }}>{{$language->language_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endforeach

                        @for($i = count($module_language_choices) + 1; $i<= 7; $i++)
                            <div class="form-group row ">
                                <label for="language_{{$i}}_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice No. ') }}{{$i}}</label>
                                <div class="col-md-6">
                                    <select name="language_{{$i}}_id" id="language_{{$i}}_id" class="custom-select">
                                        <option value="">Choice # {{$i}}</option>
                                        @if(array_key_exists($i, $module_language_choices))
                                            {{--Need to recheck array keys one is 1 another is $i-1 <option value="{{$module_language_choices[$i-1]->language_id}}" selected>{{$module_language_choices[1]->Language_name}} (current)</option> --}}
                                        @endif
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
