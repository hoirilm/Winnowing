@extends('layout.main')

@section('title', 'Advance')

@section('container')
{{-- {{dd($window2)}} --}}
<div class="container mb-5">
    <div class="box">
        <div id="chart"></div>
        <div class="col text-center">
            <a href="{{url('advance')}}"><button class="btn btn-dark" type="button" href="">
                    <i class="mdi mdi-keyboard-backspace"></i>Back</button></a>
            <a href="#detail"><button class="btn btn-primary scroll" type="button" href="">
                    <i class="mdi mdi-details"></i>Detail</button></a>
        </div>

    </div>
</div>

<div class="container mb-5" id="detail"></div>
<div class="container mt-5">
    <div class="col-12">
        <h4>Detail <i class="mdi mdi-information-outline"></i></h4>
        <hr>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Plain Text</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none" readonly>{{$source}}</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none" readonly>{{$target}}</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Translate</p>
    </div>
    
    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none" readonly>{{$translate1}}</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none" readonly>{{$translate2}}</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Preprocessing</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>{{$preprocessing1}}</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>{{$preprocessing2}}</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">K-Grams</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach($kgram1 as $item){{'['.$item.'], '}} @endforeach</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach ($kgram2 as $item){{'['.$item.'], '}} @endforeach </textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Rolling Hash</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach ($hashing1 as $item){{'['.$item.'], '}} @endforeach </textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach ($hashing2 as $item){{'['.$item.'], '}} @endforeach </textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Windows</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@for($i = 0; $i < count($window1); $i++)@for($k = 0; $k < $kgram; $k++)<?php $tampung = implode(', ', $window1[$i])?>@endfor{{'['.$tampung.'], '}}@endfor</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@for($i = 0; $i < count($window2); $i++)@for($k=0; $k < $kgram; $k++)<?php $tampung = implode(', ', $window2[$i])?>@endfor{{'['.$tampung.'], '}}@endfor</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Fingerprints</p>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Source</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach ($fingerprint1 as $item){{'['.$item.'], '}}@endforeach</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="text-center">Target</p>
                <textarea class="form-control noresize" rows="8" style="resize:none"
                    readonly>@foreach ($fingerprint2 as $item){{'['.$item.'], '}}@endforeach</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <p class="grid-header">Jaccard Coefficient</p>
    </div>
    
    <div class="col-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <textarea class="form-control noresize" rows="1" style="resize:none"
            readonly>{{'('.count($jaccard1).'/'.$jaccard3.') x 100 = '.number_format($jaccard4,2).'%'}}</textarea>
            </div>
        </div>
    </div>

</div>




<script>
    var options = { 
        chart: 
        { 
            height: 490, type: "radialBar" 
        },
    series: [
        {{number_format($jaccard4, 2)}}
        ],
    plotOptions: {
        radialBar: {
            hollow: {
                margin: 15,
                size: "85%"
            },
            dataLabels: {
                showOn: "always",
                name: {
                    offsetY: -10,
                    show: true,
                    color: "#888",
                    fontSize: "13px"
                },
                value: {
                    color: "#111",
                    fontSize: "30px",
                    show: true
                }
            }
        }
    },
    stroke: {
        lineCap: "round",
    },
    labels: ["Plagiarism Detected"]
    };
    
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    
    chart.render();
</script>


@endsection