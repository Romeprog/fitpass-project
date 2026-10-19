<!DOCTYPE html>
<html lang="en" class="app" data-page="@yield('page')">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="@yield('body_class')">
    @hasSection('nav')
        @yield('nav')
    @else
        @include('partials.header')
    @endif

    @yield('content')

    @include('partials.footer')
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>