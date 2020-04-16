<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSML Converter App</title>
    @include('partials.styles')
</head>

<body>
<div class="grid">
    <header>
        @include('partials.navbar')
    </header>
    <div class="container mt-3">
        @include('partials.alerts')
        @yield('content')
    </div>
    <footer class="mt-4">
        <div class="border-1 border-top container pt-2">
            <p class="text-center text-muted">&copy; Copyright {{ date('Y') }}. All Rights Reserved.</p>
        </div>
    </footer>
</div>
@include('partials.scripts')
</body>

</html>
