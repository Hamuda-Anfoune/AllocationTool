@extends('layouts.app')

@section('content')
{{-- {{$title}} --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-md-center"><h4>{{ __('Allcations') }}</h4></div>
                <div class="card-body">
                    @if(sizeof($allocations) > 0)
                        <h6>{{ __('Allcations') }}</h6>
                        <ul>
                            @foreach($allocations as $allocation)
                                <li><a href="/admin/allocations/show/{{$allocation->allocation_id}}">{{$allocation->allocation_id}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
