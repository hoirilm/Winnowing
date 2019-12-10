@extends('layout.main')

@section('title', 'One to Many')

@section('container')


{{-- {{dd($collection)}} --}}
<div class="col-12 mb-3">
    <h4>One to Many</h4>
    <p class="text-gray">this feature helps by detecting plagiarism in one document against multiple document</p>
    <hr>
</div>

<div class="col-12">
    <div class="alert alert-dark" role="alert">
        <strong> Your File: </strong> {{ucwords(strtolower($collection[0]['fileUploadTitle']))}}
    </div>
</div>

<div class="item-wrapper col-12">
    <div class="table-responsive">
        <table class="table info-table" id="table">
            <thead>
                <tr>
                    <th scope="col" class="text-left">#</th>
                    <th scope="col" class="text-left w-75">Corpus</th>
                    <th scope="col" class="text-center w-25">Duplicate</th>
                    <th scope="col" class="text-center w-25">Status</th>
                </tr>
            </thead>
            <tbody>

                @php $i = 1; @endphp
                @foreach ($collection as $item)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td scope="row" class="text-left">
                        <form action="/onetomany" method="post">
                            @method('put')
                            @csrf
                            <input type="hidden" name="fileUploadTitle" value="{{$item['fileUploadTitle']}}">
                            <input type="hidden" name="fileUploadContent" value="{{$item['fileUploadContent']}}">
                            <input type="hidden" name="corpusId" value="{{$item['corpusId']}}">
                            {{-- <a href="onetomany">{{ucwords(str_limit(strtolower($item['corpusTitle']), 90)). " ..."}}</a>
                            --}}
                            <button type="submit"
                                style="background:none!important; border:none; padding:0!important;">{{ucwords(str_limit(strtolower($item['corpusTitle']), 90)). " ..."}}</button>
                        </form>
                    </td>

                    <td class="text-center">{{number_format($item['similarity'], 2) .' %'}}</td>
                    <td class="text-center">
                        @if (number_format($item['similarity'], 2) < 30) <p class="badge badge-success">Low</p>
                            @elseif (number_format($item['similarity'], 2) > 30 and number_format($item['similarity'],
                            2) < 70) <p class="badge badge-warning">Medium</p>
                                @elseif (number_format($item['similarity'], 2) > 70)
                                <p class="badge badge-danger">High</p>
                                @endif
                    </td>
                </tr>
                @php if ($i++ == 10) break; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection