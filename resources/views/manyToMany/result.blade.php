@extends('layout.main')

@section('title', 'Many to Many')

@section('container')


{{-- {{dd($collection)}} --}}

<div class="col-12 mb-3">
    <h4>Many to Many</h4>
    <p class="text-gray d-inline">This feature helps detect plagiarism of many documents against many documents</p>
    <hr>

    <form action="/export1" target="_blank" method="post" class="d-inline">
        @csrf
        <input type="hidden" value="{{serialize($collection)}}" name="collection">
        <button class="btn btn-success btn-md social-btn" type="submit"><i class="mdi mdi-table-large"></i> Table Top 10 </button>
    </form>

    <form action="/export2" target="_blank" method="post" class="d-inline">
        @csrf
        <input type="hidden" value="{{serialize($collection)}}" name="collection">
        <button class="btn btn-success btn-md social-btn" type="submit"><i class="mdi mdi-table-large"></i> Table Top 5</button>
    </form>

    
</div>

@php
$keys = array_keys($collection);
$i = 0;
@endphp
{{-- @for ($i = 0; $i < count($collection); $i++)  --}}
@foreach ($collection as $item)
<div class="accordion col-md-12" id="accordionExample">
    <div class="card">

        <div class="card-body p-0 text-left" id="heading<?=$i?>">
            <div class="alert alert-dark" data-toggle="collapse" data-target="#collapse<?=$i?>" aria-expanded="true"
                aria-controls="collapse<?=$i?>" style="cursor: pointer;">
                <strong>File <?=$i+1?></strong> - {{ucwords(strtolower($item[0]['fileUpload']))}}<i
                    class="mdi mdi-menu-down float-right"></i>
            </div>
        </div>

        <div id="collapse<?=$i?>" class="collapse show" aria-labelledby="heading<?=$i?>"
            data-parent="#accordionExample">
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Corpus</th>
                            <th scope="col">Duplicate</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = 1; @endphp
                        @foreach ($collection[$keys[$i]] as $item)
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{ucwords(strtolower(str_limit($item['corpusTitle'], 120))). " ..."}}</td>
                            <td>{{number_format($item['similarity'], 2).' %'}}</td>
                            <td>
                                @if (number_format($item['similarity'], 2) < 30) <p class="badge badge-success">Low</p>
                                    @elseif (number_format($item['similarity'], 2) > 30 and
                                    number_format($item['similarity'], 2) < 70) <p class="badge badge-warning">Medium
                                        </p>
                                        @elseif (number_format($item['similarity'], 2) > 70)
                                        <p class="badge badge-danger">High</p>
                                        @endif
                            </td>
                        </tr>
                        @php if ($count++ == 10) break; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@php $i++ @endphp
@endforeach

{{-- @endfor --}}



@endsection