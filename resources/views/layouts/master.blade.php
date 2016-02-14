<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>
        Drupal Statistics
        @if (isset($title))
            &mdash; {{ $title }}
        @endif
    </title>

    <!-- Bootstrap -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    @stack('styles')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <nav class="navbar navbar-inverse" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Drupal Statistics</a>
            </div>

            <div class="collapse navbar-collapse" id="main-navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">Home</a></li>
                    <li class="{{ Request::is('about') ? 'active' : '' }}"><a href="{{ url('about') }}">About</a></li>
                    <li class="dropdown {{ Request::is('viz/*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Visualizations <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">Project Visualizations</li>
                            <li class="{{ Request::is('viz/module-downloads') ? 'active' : '' }}"><a href="{{ url('viz/module-downloads') }}">Module Downloads Bubble</a></li>
                            <li class="{{ Request::is('viz/projects-growth') ? 'active' : '' }}"><a href="{{ url('viz/projects-growth') }}">Projects Growth</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">User Visualizations</li>
                            <li class="{{ Request::is('viz/user-countries') ? 'active' : '' }}"><a href="{{ url('viz/user-countries') }}">Countries</a></li>
                            <li class="{{ Request::is('viz/user-languages') ? 'active' : '' }}"><a href="{{ url('viz/user-languages') }}">Languages spoken</a></li>
                            <li class="{{ Request::is('viz/user-expertise') ? 'active' : '' }}"><a href="{{ url('viz/user-expertise') }}">Areas of Expertise</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">CI Jobs Visualizations</li>
                            <li class="{{ Request::is('viz/ci-jobs') ? 'active' : '' }}"><a href="{{ url('viz/ci-jobs') }}">Overall CI Jobs Status</a></li>
                            <li class="{{ Request::is('viz/ci-jobs-reasons') ? 'active' : '' }}"><a href="{{ url('viz/ci-jobs-reasons') }}">Reasons for testing</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <footer class="navbar navbar-inverse footer">
        <div class="container">
            <div class="navbar-text pull-left">
                Drupal is a <a href="http://drupal.com/trademark" target="_blank">registered trademark</a> of <a href="http://buytaert.net/" target="_blank">Dries Buytaert</a>.
            </div>
        </div>
    </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    @stack('scripts')
</body>
</html>
