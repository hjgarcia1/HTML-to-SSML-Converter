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

<div class="container mt-3">
    @include('partials.alerts')
    <h1>SSML List</h1>
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
        @forelse($ssmls as $ssml)
            <tr>
                <td>{{ $ssml->id }}</td>
                <td>{{ $ssml->title }}</td>
                <td><a href="{{ $ssml->link }}" target="_blank">{{ $ssml->link }}</a></td>
                <td>
                    <a class="btn btn-danger btn-sm btn-flat" href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $ssml->id }}').submit();">
                        x
                    </a>

                    <form action="{{ url('/ssml/' . $ssml->id) }}" method="POST" id="delete-form-{{ $ssml->id }}" style="display: none;">
                        {{csrf_field()}}
                        {{ method_field('DELETE') }}
                        <input type="hidden" value="{{ $ssml->id }}" name="id">
                    </form>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="4" class="text-center">No Records</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@include('partials.scripts')
</body>

</html>
