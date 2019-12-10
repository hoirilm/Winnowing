<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\WinnowingExport;


class ExportController extends Controller
{
    public function export1(Request $request)
    {
        // dd($request->collection);

        $collection = unserialize($request->collection);

        return view('manytomany.export', compact('collection'));
    }

    public function export2(Request $request)
    {
        // dd($request->collection);

        $collection = unserialize($request->collection);

        return view('manytomany.export2', compact('collection'));
    }
}
