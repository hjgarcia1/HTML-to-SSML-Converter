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
    <div class="col-md-6 offset-md-3">
        <h1>SSML Converter</h1>
        <form method="post" action="/convert">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name">Name</label> <input type="text" id="name" class="form-control">
                {!! $errors->first('name','<div class="invalid-feedback">:message</div>') !!}
            </div>

            <div class="form-group">
                <label for="html">HTML</label> <textarea name="html" id="html" cols="30" rows="10"
                                                         class="form-control"></textarea>
            </div>
            {!! $errors->first('html','<div class="invalid-feedback">:message</div>') !!}
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@include('partials.scripts')
</body>

</html>
