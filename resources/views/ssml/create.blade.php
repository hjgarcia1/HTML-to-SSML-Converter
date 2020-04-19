@extends('layouts.main')

@section('content')

    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            <h1>New SSML</h1>
            <form method="post" action="/store">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="title">Name</label> <input type="text" id="title" name="title" value="{{ old('title') }}"
                        class="form-control">
                    {!! $errors->first('title','<div class="invalid-feedback">:message</div>') !!}
                </div>

                <div class="form-group">
                    <label for="html">HTML</label> <textarea name="html" id="html" cols="30" rows="10"
                        class="form-control">{{ old('html') }}</textarea>
                </div>
                {!! $errors->first('html','<div class="invalid-feedback">:message</div>') !!}
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="/" type="submit" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>


@stop

