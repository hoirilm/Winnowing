@extends('layout.main')

@section('title', 'One to Many')

@section('container')

<div class="col-12 mb-3">
    <h4>One to Many</h4>
    <p class="text-gray">This feature helps by detecting plagiarism in one document against multiple document</p>
    <hr>
</div>

@if (session('corpus'))
<div class="alert alert-danger col-12">
    {{ session('corpus') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger col-12">
    {{ session('error') }}
</div>
@endif

<div class="grid col-2"></div>
<div class="grid col-8">
    <div class="grid-body p-0">
        <div class="item-wrapper">

            <form method="post" action="/onetomany" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="inputGroupFile04">Upload Document</label>
                    <div class="custom-file">
                        <input type="file"
                            class="custom-file-input form-control @error('document') is-invalid @enderror @if(session('fail')) is-invalid @endif"
                            name="document" id="document" aria-describedby="inputGroupFileAddon04">
                        <label class="custom-file-label" for="inputGroupFile04">
                            <p>Choose file</p>
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
<div class="grid col-2"></div>
@endsection