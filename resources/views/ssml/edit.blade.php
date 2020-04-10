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
    <div class="col-md-6 offset-md-3">
        <h1>Edit SSML</h1>
        <form method="post" action="/ssml/{{$ssml->id}}">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="title">Title</label> <input type="text" id="title" name="title" value="{{ old('title', $ssml->title) }}" class="form-control">
                {!! $errors->first('title','<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="form-group">
                <p><strong>Current SSML:</strong></p>
                <p><a href="{{ $ssml->link }}" target="_blank">{{ $ssml->link }}</a></p>
            </div>

            <div class="form-group">
                <label for="html">HTML</label> <textarea name="html" id="html" cols="30" rows="10"
                    class="form-control">{{ old('html', $ssml->html) }}</textarea>
            </div>
            {!! $errors->first('html','<div class="invalid-feedback">:message</div>') !!}
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/" type="submit" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
</div>

@include('partials.scripts')
</body>

</html>
