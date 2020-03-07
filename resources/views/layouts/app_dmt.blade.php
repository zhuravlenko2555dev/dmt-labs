<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Теорія прийняття рішень</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
{{--    defer--}}
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .no-container {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    </style>
</head>
<body>
<div id="app">
    <main class="py-4">
        @include('dmt.menu')
        @yield('content')
    </main>
</div>
</body>
</html>
