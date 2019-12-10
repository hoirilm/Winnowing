<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    
    public function index()
    {
        return view('index');
    }

    public function corpus()
    {
        return view('corpus.index');
    }

    public function advance()
    {
        return view('advance.index');
    }

    public function translate()
    {
        return view('translateDoc.index');
    }

    public function oneToMany()
    {
        return view('oneToMany.index');
    }

    // public function oneToManyResult()
    // {
    //     return view('oneToMany.result');
    // }

    public function manyToMany()
    {
        return view('manyToMany.index');
    }

    public function manyToManyResult()
    {
        return view('manyToMany.result');
    }

}
