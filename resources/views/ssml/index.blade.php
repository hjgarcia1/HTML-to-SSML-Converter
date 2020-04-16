@extends('layouts.main')

@section('content')
    <div class="d-flex">
        <h1>SSML List</h1>
        <form class="form-inline d-flex mb-3 ml-auto" method="get" action="/">
            <div class="form-group mx-sm-3 mb-2">
                <label for="query" class="sr-only">Search</label> <input type="text" class="form-control" id="query"
                    placeholder="Search..." value="{{ request('query') }}" name="query">

            </div>
            <button type="submit" class="btn btn-primary mb-2">Search</button>
            @if(request()->has('query'))
                <a href="/" class="btn-danger btn mb-2 ml-2">Clear</a>
            @endif
        </form>
    </div>
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
                <td style="width: 20%">
                    <a href="/ssml/{{$ssml->id}}" class="btn btn-secondary btn-sm btn-flat"> <i
                            class="fa fa-pencil"></i> </a>

                    <a href="{{ $ssml->mp3 }}" class="btn btn-secondary btn-sm btn-flat" download target="_blank"> <i
                            class="fa fa-file-audio-o"></i> </a> <a class="btn btn-danger btn-sm btn-flat" href="#"
                        onclick="event.preventDefault(); document.getElementById('delete-form-{{ $ssml->id }}').submit();">
                        <i class="fa fa-times"></i> </a>

                    <form action="{{ url('/ssml/' . $ssml->id) }}" method="POST" id="delete-form-{{ $ssml->id }}"
                        style="display: none;">
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
    {{ $ssmls->links() }}
@stop
