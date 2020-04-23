@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            <h1>Edit SSML</h1>
            <form method="post" action="/ssml/{{$ssml->id}}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="title">Title</label> <input type="text" id="title" name="title"
                        value="{{ old('title', $ssml->title) }}" class="form-control">
                    {!! $errors->first('title','<div class="invalid-feedback">:message</div>') !!}
                </div>

                <div class="form-group">
                    <p><strong>Current SSML:</strong></p>
                    <p><a href="{{ $ssml->link }}" target="_blank">{{ $ssml->link }}</a></p>
                </div>

                <div class="form-group">
                    <p><strong>Current MP3:</strong></p>
                    <p><a href="{{ $ssml->mp3 }}" target="_blank" download="">{{ $ssml->mp3 }}</a></p>
                </div>

                <div class="form-group">
                    <label for="html">HTML</label> <textarea name="html" id="html" cols="30" rows="10"
                        class="form-control">{{ old('html', $ssml->html) }}</textarea>
                </div>
                <p id="characterCount" class="text-danger"></p>
                {!! $errors->first('html','<div class="invalid-feedback">:message</div>') !!}

                <div class="d-lg-flex justify-content-end p-3 bg-light">
                    <button type="submit" class="btn btn-primary mr-lg-2 d-inline-block">Update</button>
                    <a href="/" type="submit" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
@stop
