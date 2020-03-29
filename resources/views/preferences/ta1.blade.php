@print_r($request->session()->all());

@{{ $data->'email' }}
<@php
    echo session()->get('email');
@endphp
