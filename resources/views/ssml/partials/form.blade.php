<form class="d-flex flex-column flex-lg-row flex-grow-1 bg-light border"
    method="get"
    action="/">
    <div class="form-group flex-grow-1 mx-3 mt-3 m-lg-3">
        <label for="query"
            class="sr-only">Search</label> <input type="text"
            class="form-control w-100"
            id="query"
            placeholder="Search..."
            value="{{ request('query') }}"
            name="query">
    </div>
    <button type="submit"
        class="btn btn-primary mx-3 mb-3 ml-lg-0 my-lg-3 mr-lg-3">Search</button>
    @if(request()->has('query'))
    <a href="/"
        class="btn btn-secondary mx-3 mb-3 ml-lg-0 my-lg-3 mr-lg-3">Clear</a>
    @endif
</form>