<!DOCTYPE html>
<html lang="en" class="auth" data-page="@yield('page')">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    @yield('content')

    @hasSection('script')
        <script src="{{ asset('js/script.js') }}"></script>
    @endif
</body>

</html>