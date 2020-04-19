@extends('layouts.main')

@section('content')
    <div class="d-lg-flex align-items-center mb-4">
        <h1 class="mb-0 mr-4">SSML List</h1>
        <form class="d-flex flex-column flex-lg-row flex-grow-1 bg-light border" method="get" action="/">
            <div class="form-group flex-grow-1 mx-3 mt-3 m-lg-3">
                <label for="query" class="sr-only">Search</label> <input type="text" class="form-control w-100" id="query"
                    placeholder="Search..." value="{{ request('query') }}" name="query">
            </div>
            <button type="submit" class="btn btn-primary mx-3 mb-3 ml-lg-0 my-lg-3 mr-lg-3">Search</button>
            @if(request()->has('query'))
                <a href="/" class="btn btn-secondary mx-3 mb-3 ml-lg-0 my-lg-3 mr-lg-3">Clear</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($ssmls as $ssml)
                <tr>
                    <td>{{ $ssml->id }}</td>
                    <td>{{ $ssml->title }}</td>
                    <td style="width: 20%">
                        <a href="/ssml/{{$ssml->id}}" class="btn btn-primary btn-sm mb-2 btn-flat"> <i
                                class="fa fa-pencil"></i> </a>

                        <a href="{{ $ssml->link }}" class="btn btn-secondary btn-sm mb-2 btn-flat" target="_blank"><i class="fa fa-file-code-o"></i></a>

                        <a href="{{ $ssml->mp3 }}" class="btn btn-secondary btn-sm mb-2 btn-flat" download target="_blank"> <i
                                class="fa fa-file-audio-o"></i> </a>

                        <a class="btn btn-danger btn-sm mb-2 btn-flat" href="#"
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
    </div>
    {{ $ssmls->links() }}
@stop
