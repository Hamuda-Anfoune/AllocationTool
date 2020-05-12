<nav class="navbar navbar-expand-md navbar-inverse bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"><i class="fa fa-bars" aria-hidden="true"></i></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>
            <ul class="navbar-nav">
                <li class="nav-item active">
                  <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="/about">Contact us</a>
                </li>
                @if (session('account_type_id') == 002)
                    <li class="nav-item  dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Convenors') }}
                          </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="nav-link" href="/module/prefs/add">{{ __('Submit Preferences') }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="nav-link" href="/module/convenor">{{ __('All Your Modules') }}</a>
                        </div>
                    </li>
                @endif
                @if (session('account_type_id') == 003 || session('account_type_id') == 004)
                    <li class="nav-item  dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Teaching Assistants') }}
                          </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="nav-link" href="/TA/prefs/add">{{ __('Submit Preferences') }}</a>
                            <a class="nav-link" href="/modules/prefs/all">{{ __('Modules\' Preferences') }}</a>
                        </div>
                    </li>
                @endif
                @if (session('account_type_id') == 000 || session('account_type_id') == 001)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Admins') }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="nav-link" href="/admin/dashboard">{{ __('Dashboard') }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="nav-link" href="/admin/config">{{ __('Configuration') }}</a>
                            <div class="dropdown-divider"></div>
                            @if (session('account_type_id') == 000)
                                <a class="nav-link" href="/admin/university-users/add">{{ __('Add New University User' ) }}</a>
                            @endif
                            <a class="nav-link" href="/admin/modules/add">{{ __('Add New Modules' ) }}</a>
                            <a class="nav-link" href="/modules/prefs/all">{{ __('Modules\' Preferences') }}</a>
                        </div>
                    </li>
                @endif
            </ul>
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item dropdown">
                            {{-- <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> --}}
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Register') }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('User') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="nav-link" href="/admin/university/signup ">{{ __('Univesity') }}</a>
                                </div>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
