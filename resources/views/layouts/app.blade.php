<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>How much does it cost to buy all Steam games</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<div id="app" class="container">
    @yield('content')
</div>
<footer class="container">
    <div class="row">
        <div class="col-lg-12">
            <p>&copy {{ date('Y') }} <a href="https://seewang.me">seewang.me</a></p>
        </div>
    </div>
</footer>
<script src="{{ mix('js/app.js') }}"></script>
<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
@include('layouts.analytics')
</body>
</html>
