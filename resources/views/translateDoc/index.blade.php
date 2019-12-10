@extends('layout.main')

@section('title', 'Translate Documents')

@section('container')

<div class="col-12">
    <h4>Translate Documents</h4>
    <p class="text-gray">This feature helps translate documents in large numbers</p>
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

<div class="grid col-2"></div>
<div class="grid col-8">
    <div class="grid-body p-0">
        <div class="item-wrapper">

            <form method="post" action="/translate" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="inputGroupFile04">Upload Document(s)</label>
                    <div class="custom-file">
                        <input type="file"
                            class="custom-file-input form-control @error('document') is-invalid @enderror @if(session('fail')) is-invalid @endif"
                            name="document[]" id="document" aria-describedby="inputGroupFileAddon04" multiple>
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