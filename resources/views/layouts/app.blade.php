<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AllocationTool') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/all.js') }}" defer></script> <!-- FONTAWESOME -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> --}}

    <script src="{{ asset('js/additionalDivs.css') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
    {{-- <link href="https://fonts.googleapis.com/css?family=Helvetica_Neue" rel="stylesheet"> --}}

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.css') }}" rel="stylesheet"> <!-- FONTAWESOME -->

</head>
<body>
    <div id="app">
        @include('inc.navbar')
        <main class="container py-4">
            @include('inc.messages')
            @yield('content')
        </main>
    </div>




    <script>

        // function showDiv(id, selff) {
        //     document.getElementById(id).style.display = 'block';
        //     document.getElementById(selff).style.display = 'none';
        //     // this.style.visibility = 'hidden';
        // }

        // var moduleDivCounter = 0;
        // // Contains IDs of the programming module choices hidden divs in both preferences submission pages
        // let moduleDivs = [
        //     'module_4_div',
        //     'module_5_div',
        //     'module_6_div',
        //     'module_7_div',
        //     'module_8_div',
        //     'module_9_div',
        //     'module_10_div',
        // ]
        // function showModuleDivs()
        // {
        //     document.getElementById(moduleDivs[moduleDivCounter]).style.display = 'block';
        //     if(moduleDivCounter == 6) document.getElementById('mod_btn').style.display = 'none';

        //     moduleDivCounter++;
        // }


        // var languageDivCounter = 0;
        // // Contains IDs of the programming language choices hidden divs in both preferences submission pages
        // let langDivs = [
        //     'language_4_div',
        //     'language_5_div',
        //     'language_6_div',
        //     'language_7_div',
        // ]
        // function showLangDivs()
        // {
        //     document.getElementById(langDivs[languageDivCounter]).style.display = 'block';
        //     if(languageDivCounter == 3) document.getElementById('lang_btn').style.display = 'none';

        //     languageDivCounter++;
        // }


        $(document).ready(
            function() {

                // let id = "#smart_01";

                // $("#btttn").click(function() {
                //     $("#id").toggle("slow");
                // });
        });
    </script>

</body>
</html>
