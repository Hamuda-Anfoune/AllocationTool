@if(count($errors) > 0)
    @foreach ($errors->all(); as $error)
    {{-- ABOVE:
        ->all(): cus it is an object
        SOMETIMES, WOULD NOT WORK WITHOUT IT --}}
        <div class="alert alert-danger">
            {{$error}}
        </div>
    @endforeach
@endif

@if (session('success'))
    <div class="alert alert-success">
        <strong>Success: </strong>{{session('success')}}
    </div>
@endif

@if (session('alert'))
    <div class="alert alert-danger">
        <strong>Alert: </strong>{{session('alert')}}
    </div>
@endif

@if (session('allocated'))
    <div id="allocated_div" class="alert alert-warning text-center" role="alert" style="display:block; border:1px solid orange; position: absolute; left:50%; top:50%; transform: translate(-50%, -50%); z-index:10">
        <div class="col-md-12 text-right">
            <a class="" type="button" onclick='dismissAllocatedDiv()' id="lang_btn">X</a>
        </div>
        <div>
            <strong>Allocation:<br></strong>{{session('allocated')}}<br>You can view all allocations <a href="/admin/allocations">here</a>.
        </div>
    </div>
@endif
