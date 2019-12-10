<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\stopword;
use Illuminate\Support\Facades\Storage;
use \Statickidz\GoogleTranslate; // untuk google translate

class TranslateController extends Controller
{
    public function translate(Request $request)
    {
        // dd($request->document);

        $get_stopwords = stopword::select('data')->get()->toArray();
        $stopwords = array_pluck($get_stopwords, 'data');

        $request->validate([
            'document' => 'required',
            'document.*' => 'required'
        ]);

        foreach ($request->document as $file) {
            $title[] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // ambil nama file
            $file_extension[] = $file->getClientOriginalExtension(); // ambil jenis ekstensi file
            $content[] = file_get_contents($file); // ambil content file
        }

        foreach ($file_extension as $value) {
            if ($value !== 'txt') {
                return redirect('/translate')->with('fail', 'The document must be a file of type: .txt');
                die;
            }
        }

        for ($i = 0; $i < count($request->document); $i++) {
            $replace = preg_replace('/ /', '`', $content[$i]); //replace spasi dengan (`) agar bisa digunakan untuk explode
            $explode = explode("`", $replace); //memecah string berdasarkan (`)
            $selection = array_intersect($explode, $stopwords); // cari nilai yang sama antara explode dengan stopword

            if (count($selection) > (20 / 100) * count($explode)) { // dihitung apakah jumlah stopword (bhs inggris dalam string mencapai 20% atau tidak)
                $source = 'en';
                $target = 'en';
            } else {
                $source = 'in';
                $target = 'en';
            }

            $filter_string = preg_replace('/["]/', '', $content[$i]);  // menghilangkan tanda " karena mengganggu hasil terjemahan
            $stringSource = $filter_string;

            $trans = new GoogleTranslate();
            $result[] = $trans->translate($source, $target, $stringSource);

            // $total = (20 / 100) * count($explode);
            // dd($source, $target, $content[$i], $result, 'total kata: ' . count($explode), 'batas seleksi: ' . $total, 'english', $selection);
        }

        for ($i=0; $i < count($request->document); $i++) {
            Storage::put($title[$i].'.txt', $result[$i]); // tersimpan di folder public/translated
        }

        return redirect('/translate')->with('success', "Translating documents successfully. Data stored in '/public/translated' folder"); 
        
    }
}
