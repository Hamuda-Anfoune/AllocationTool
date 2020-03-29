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
