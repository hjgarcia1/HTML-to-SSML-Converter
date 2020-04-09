<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSML Converter App</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <style>
        .invalid-feedback {
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>

</head>

<body>
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

<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
