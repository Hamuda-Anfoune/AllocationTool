
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3>Welcome to AlloTool!</h3>
                    <p>All that straneous work of allocating teaching assistants to modules is done in a few clicks!</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
