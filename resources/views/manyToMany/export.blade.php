<style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .tg td {
        font-family: Arial, sans-serif;
        font-size: 14px;
        padding: 10px 5px;
        border-style: solid;
        border-width: 0px;
        overflow: hidden;
        word-break: normal;
        border-top-width: 1px;
        border-bottom-width: 1px;
        border-color: black;
    }

    .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 10px 5px;
        border-style: solid;
        border-width: 0px;
        overflow: hidden;
        word-break: normal;
        border-top-width: 1px;
        border-bottom-width: 1px;
        border-color: black;
    }

    .tg .tg-yjqx {
        font-weight: bold;
        background-color: #d3d8df;
        border-color: inherit;
        text-align: left;
        vertical-align: middle
    }

    .tg .tg-lboi {
        border-color: inherit;
        text-align: left;
        vertical-align: middle
    }

    .tg .tg-9wq8 {
        border-color: inherit;
        text-align: center;
        vertical-align: middle
    }

    .tg .tg-uzvj {
        font-weight: bold;
        border-color: inherit;
        text-align: center;
        vertical-align: middle
    }

    .tg .tg-g7sd {
        font-weight: bold;
        border-color: inherit;
        text-align: left;
        vertical-align: middle
    }

    .tg .tg-yz93 {
        border-color: inherit;
        text-align: right;
        vertical-align: middle
    }

    .tg .tg-0pky {
        border-color: inherit;
        text-align: left;
        vertical-align: top
    }
</style>

{{-- {{dd($collection)}} --}}

@php
$keys = array_keys($collection);
$i = 0;
@endphp
<div class="col-md-12">
    <table class="tg w-100">
        @foreach ($collection as $item)
        <tr>
            <th class="tg-yjqx" colspan="3">File - <?=$i+1?> {{ucwords(strtolower($item[0]['fileUpload']))}}</th>
        </tr>
        <tr>
            <td class="tg-uzvj">#</td>
            <td class="tg-g7sd">Corpus</td>
            <td class="tg-uzvj">Winnowing</td>
            {{-- <td class="tg-uzvj">Status</td> --}}
        </tr>
        @php $count = 1; @endphp
        @foreach ($collection[$keys[$i]] as $item)
        <tr>
            <td class="tg-uzvj">{{$loop->iteration}}</td>
            <td class="tg-lboi">{{ucwords(strtolower($item['corpusTitle']))}}</td>
            <td class="tg-yz93">{{number_format($item['similarity'], 2).' %'}}</td>
            {{-- <td class="tg-9wq8">
                @if (number_format($item['similarity'], 2) < 30)
                    <p style="background-color:green; color:white">Low</p>
                @elseif (number_format($item['similarity'], 2) > 30 and number_format($item['similarity'], 2) < 70) 
                    <p style="background-color:yellow; color:white">Medium</p>
                @elseif (number_format($item['similarity'], 2) > 70)
                    <p style="background-color:red; color:white">High</p>
                @endif
            </td> --}}
        </tr>
        @php if ($count++ == 10) break; @endphp
        @endforeach
        @php $i++ @endphp
        @endforeach
    </table>
</div>