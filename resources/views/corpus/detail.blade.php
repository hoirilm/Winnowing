@extends('layout.main')

@section('title', 'Corpus')

@section('container')

{{-- {{dd($massage)}} --}}

<div class="col-12 mb-1">
    <h4>Corpus</h4>
    <p class="text-gray">Document that has been saved</p>
    <hr>
</div>
{{-- {{dd($document)}} --}}
<div class="grid col-12">
    <p class="grid-header">Detail</p>
    <div class="card w-80 p-0">
        <div class="card-body p-0">
            <h5 class="card-title py-2">{{ $document->title }}</h5>
            <p class="card-text py-2 text-justify">{{ $document->content }}</p>
            <div class="pt-4 float-right">
                <form action="/corpus/{{ $document->id}}" method="post" class="d-inline">
                    @method('delete') {{-- //untuk mengelabuhi form. agar tidak bisa diakses menggunakan url browser --}}
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                {{-- <a href="#" class="btn btn-danger">Hapus</a> --}}
                <a href="/corpus" class="btn btn-primary">Kembali</a>
            </div>

        </div>
    </div>
</div>

@endsection