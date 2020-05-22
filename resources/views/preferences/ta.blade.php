{{-- <main class="container m-0" >

    <div class="col-md-12" style="background-color:brown; background-image: url('/images/about.png'); height:12vw">
        //
    </div>
</main> --}}
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
<link href="{{ asset('css/all.css') }}" rel="stylesheet"> <!-- FONTAWESOME -->

<style>
body, html {
  height: 100%;
  margin: 0;
}

.bg {
  /* The image used */
  background-image: url("/images/about.png");

  /* Full height */
  height: 100%;

  /* Center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
</head>
<body>

<div class="bg">
    <div class="col-md-12 row" style="height: 100%">
        <div class="col-md-6" style="position: absolute; top:10%">
            <div class="col-md-9 text-center" style="color:white">
                <p>Each semester, the staff of Teaching Assistants at universities changes, that change requires a change in their duties and timetables,
                    and even if the staff remains the same; their priorities and preferences may change, so may the priorities and preferences of the module convenors.
                     All this requires a new allocation of teaching assistants to the taught modules each semester, which is a time-consuming and strenuous process.
                </p>
            </div>
        </div>
        <div class="col-md-6" style="position: absolute; left: 60%; top:70%">
            <div class="col-md-9 text-center" style="color:white">
                <p>This project is a web app that allocates Teaching Assistants to modules based on system-native and provided criteria,
                    these criteria include preferences of both Teaching Assistants and module convenors,
                    Teaching Assistants’ availability and previous experience with the module, Visa working-hours restraints, etc.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
