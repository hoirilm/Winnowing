@extends('layout.main')

@section('title', 'Advance')

@section('container')

<div class="col-12 md-5">
    <h4>Advance Mode</h4>
    <p class="text-gray">You can set it manually</p>
    <hr>
</div>

@if (session('error'))
<div class="alert alert-danger col-12">
    {{ session('error') }}
</div>
@endif

<div class="container p-2">
    <form method="POST" action="/advance">
        {{-- @method('put') --}}
        @csrf
        <div class="row">
            <div class="form-group col-sm">
                <label class="font-weight-bold" for="source">Source</label>
                <textarea class="form-control noresize @error('source') is-invalid @enderror" name="source" id="source"
                    rows="10" style="resize:none" placeholder="write something here..">{{ old('source') }}</textarea>
                @error('source')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group col-sm">
                <label class="font-weight-bold" for="target">Target</label>
                <textarea class="form-control noresize @error('target') is-invalid @enderror" name="target" id="target"
                    rows="10" style="resize:none" placeholder="write something here..">{{ old('target') }}</textarea>
                @error('target')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 mb-2">
                <label for="kgram">K-Gram</label>
                <input type="text" class="form-control @error('kgram') is-invalid @enderror" id="kgram" name="kgram"
                    value="{{ old('kgram') }}" placeholder="K-Gram">
                @error('kgram')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="col-lg-2 mb-2">
                <label for="wgram">W-Gram</label>
                <input type="text" class="form-control @error('wgram') is-invalid @enderror" id="wgram" name="wgram"
                    value="{{ old('wgram') }}" placeholder="W-Gram">
                @error('wgram')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="col-lg-2 mb-2">
                <label for="base">Base</label>
                <input type="text" class="form-control @error('wgram') is-invalid @enderror" id="base" name="base"
                    value="{{ old('base') }}" placeholder="Base">
                @error('base')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="col mt-2">

                <button class="btn btn-dark btn-lg float-right social-btn" type="submit">
                    <i class="mdi mdi-arrow-right-bold-circle"></i>Process</button>
            </div>
        </div>
    </form>
</div>

@endsection