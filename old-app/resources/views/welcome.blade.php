@extends('layouts.app')

@section('content')
<div class="jumbotron text-center">
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <h3>Welcome to AlloTool!</h3>
        <br>
        <p>Each semester, the staff of Teaching Assistants at universities changes, that change requires a change in their duties and timetables,
             and even if the staff remains the same; their priorities and preferences may change, so may the priorities and preferences of the module convenors.
              All this requires a new allocation of teaching assistants to the taught modules each semester, which is a time-consuming and strenuous process.
              This project is a web app that allocates Teaching Assistants to modules based on system-native and provided criteria,
             these criteria include preferences of both Teaching Assistants and module convenors,
             Teaching Assistants’ availability and previous experience with the module, Visa working-hours restraints, etc.</p>
    </div>
</div>
@endsection

