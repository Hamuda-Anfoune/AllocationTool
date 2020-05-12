@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center">
                    <a class="btn btn-info btn-sm" data-toggle="collapse" href="#allocation_data" role="button" aria-expanded="false" aria-controls="allocation_data">
                        {{ __('Allocation Data') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="collapse" id="allocation_data">
                        <h5>Allocation data based on modules</h5>
                        @if(sizeof($allocation_data) > 0)
                            <h6><strong></strong></h6>
                            @foreach($allocation_data as $module_data)
                                {{$module_data[0]->module_id}}
                                <ul>
                                    @foreach($module_data as $allocation)
                                        {{-- <li><a href="/admin/allocations/edit/{{$allocation->module_id}}/{{$allocation->ta_id}}">{{$allocation->ta_id}}</a></li> --}}
                                        <li>{{$allocation->ta_id}}</li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-header text-md-center">
                    <a class="btn btn-info btn-sm" data-toggle="collapse" href="#ta_allocation_data" role="button" aria-expanded="false" aria-controls="ta_allocation_data">
                        {{ __('Teaching Assistants Allocation Data') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="collapse" id="ta_allocation_data">
                        @if(sizeof($ta_allocation_data) > 0)
                            <h6></h6>
                            @foreach($ta_allocation_data as $ta)
                                <ul>
                                    <li>{{$ta->ta_id}}
                                        <ul>
                                            <li>Allocated contact hours: {{$ta->contact_hours}}</li>
                                            <li>Allocated marking hours: {{$ta->marking_hours}}</li>
                                        </ul>
                                    </li>
                                </ul>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
