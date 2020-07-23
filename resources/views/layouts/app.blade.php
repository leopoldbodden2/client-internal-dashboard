<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/gif" href="favicon.gif">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/jq-3.3.1/dt-1.10.18/r-2.2.2/datatables.min.css"/>

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body>
<div id="progress-bar"></div>

@if(request()->has('iframe'))
    {{-- <div class="container-fluid bg-grey">
        <div class="row">
            <nav class="col">
                <ul class="nav nav-pills flex-row nav-justified">
                    @include('includes.nav-list')
                </ul>
            </nav>
        </div>
    </div> --}}
@else
    <nav class="navbar navbar-light bg-white {{ request()->has('iframe') ? '' : 'fixed-top' }} p-0 shadow-sm justify-content-start">
        <div class="navbar-brand text-orange align-top">
            <span class="far fa-chart-line"></span>
        </div>
        <div class="navbar-text">
            <h1>Website Dashboard</h1>
            <p>{{ auth()->check()?auth()->user()->display_name:'' }}&nbsp;</p>
        </div>
        @stack('nav')
    </nav>
    <div class="container-fluid bg-grey">
        <div class="row">
            <nav class="col-auto bg-white sidebar">
                <ul class="nav flex-column">
                    @include('includes.nav-list')
                </ul>
            </nav>
        </div>
    </div>
@endif
@if(request()->has('iframe'))
    <main id="app" class="bg-grey pt-5">
        @include('includes.errors')
        @include('includes.alerts')
        @yield('content')
    </main>
@else
    <div class="container-fluid bg-grey">
        <div class="row">
            <main id="app" role="main" class="col">
                @include('includes.errors')
                @include('includes.alerts')
                @yield('content')
            </main>
        </div>
    </div>
@endif
@stack('modals')
<script>window.APP_URL = "{{ url('/') }}";</script>
<!-- ChartJs -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<!-- Scripts -->
<script src="{{ asset(mix('js/app.js')) }}"></script>
@stack('scripts')
</body>
</html>
