<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSML Converter App</title>

    @include('partials.styles')

</head>

<body>

@include('partials.navbar')

<div class="container">
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Link</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ssmls as $ssml)
            <tr>
                <td>{{ $ssml->id }}</td>
                <td>{{ $ssml->title }}</td>
                <td><a href="{{ $ssml->link }}" target="_blank">{{ $ssml->link }}</a></td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@include('partials.scripts')
</body>

</html>
