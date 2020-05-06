@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center">
                    <h4>Preferences of {{$module_basic_prefs[0]->module_name}} for {{$academic_year}}</h4>
                </div>
                <div class="card-body">
                    <h5></h5>
                    <form action="{{ route('showAllModulePrefs') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Module Name') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$module_basic_prefs[0]->module_name}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Module ID') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$module_basic_prefs[0]->module_id}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_of_assistants" class="col-md-4 col-form-label text-md-right">{{ __('Number of Assistants Required') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$module_basic_prefs[0]->no_of_assistants}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Number of Contact Hours') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$module_basic_prefs[0]->no_of_contact_hours}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_of_marking_hours" class="col-md-4 col-form-label text-md-right">{{ __('Number of Marking Hours') }}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$module_basic_prefs[0]->no_of_marking_hours}}" aria-describedby="helpId"  disabled>
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

                        @foreach ($module_language_choices as $language)
                        <div class="form-group row ">
                            <label for="language_1_id" class="col-md-4 col-form-label text-md-right">{{ __('Language Choice No. ') }}{{$language->priority}}</label>
                            <div class="col-md-6">
                                <input type="text" name="" id="module_id" class="form-control" placeholder="{{$language->Language_name}}" aria-describedby="helpId"  disabled>
                            </div>
                        </div>
                        @endforeach

                        <br>
                        <hr>
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button href="/modules/prefs/all" class="btn btn-primary">
                                    {{ __('Back to all modules') }}
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
