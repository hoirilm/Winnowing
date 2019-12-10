@extends('layout.main')

@section('title', 'Corpus')

@section('container')
{{-- {{dd($documents)}} --}}
{{-- {{dd($massage)}} --}}

<div class="col-12 mb-1">
    <h4>Corpus</h4>
    <p class="text-gray">Document that has been saved</p>
    <hr>
</div>

@if (session('success'))
<div class="alert alert-success col-12">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger col-12">
    {{ session('error') }}
</div>
@endif

<div class="grid col-md-8">
    <p class="grid-header">Corpus</p>
    <div class="item-wrapper">
        <div class="table-responsive">
            <table class="table info-table" id="table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Title</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($documents as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td class="text-left">{{ucwords(strtolower(str_limit($item->title, 80))) . " ..."}}</td>
                        <td><a href="/corpus/{{ $item->id }}" class="badge badge-primary">Detail</a></td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="item-wrapper col-12 py-4">
                <div class="float-left py-2">
                    <p class="text-muted">{{ $documents->currentPage() }} of {{ $documents->lastPage() }} pages |
                        {{$documents->total()}} document(s)</p>
                </div>

                <div class="float-right">
                    {{ $documents->links() }}
                </div>
            </div>

        </div>
    </div>
</div>


<div class="grid col-sm-12 col-md-4">
    <p class="grid-header">Action</p>
    <div class="grid-body">
        <div class="item-wrapper">
            <form method="GET" action="/corpus/search">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" class="form-control @error('search') is-invalid @enderror" name="search"
                        id="search" value="{{ old('search') }}" placeholder="Search here.." autocomplete="off">
                    @error('search')
                    <div class="invalid-feedback pt-2">{{$message}}</div>
                    @enderror
                    {{-- <input type="submit"> --}}
                </div>
            </form>
            <form method="POST" action="/corpus" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="inputGroupFile04">Upload Document(s)</label>
                    <div class="custom-file">
                        <input type="file"
                            class="custom-file-input form-control @error('document') is-invalid @enderror @if(session('fail')) is-invalid @endif"
                            name="document[]" id="document" aria-describedby="inputGroupFileAddon04" multiple>
                        <label class="custom-file-label" for="inputGroupFile04">
                            Choose file..
                        </label>

                        @error('document')
                        <div class="invalid-feedback pt-2">{{$message}}</div>
                        @enderror

                        {{-- memanggil error menggunakan session --}}
                        @if (session('fail'))
                        <div class="invalid-feedback pt-2">{{ session('fail') }}</div>
                        @endif
                    </div>
                </div>
                <button class="btn btn-dark float-right btn-block social-btn" type="submit">
                    <i class="mdi mdi-upload"></i>Upload</button>
            </form>
        </div>
    </div>
</div>


@endsection