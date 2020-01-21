<?php

namespace App\Http\Controllers;

use App\Document;
use App\stopword;
use Illuminate\Http\Request;
use \Statickidz\GoogleTranslate;
// use Illuminate\Support\Facades\Input;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::orderBy('id', 'desc')->paginate(10);

        // return $documents; die;
        return view('corpus.index', ['documents' => $documents]);
    }

    public function search(Request $request)
    {
        // $request->validate([
        //     'search' => 'required',
        // ]);

        $search = $request->search; // menangkap data pencarian

        // query builder untuk memanggil data documents yang mirip dengan pencarian
        $documents = Document::where('title', 'like', "%" . $search . "%")->paginate(10);
        $documents->appends($request->only('search')); // digunakan agar pagination tetap seasuai dengan pencarian (append url)

        return view('corpus.index', ['documents' => $documents]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        function translate($string, $stopwords)
        {

            $replace = preg_replace('/ /', '`', $string); //replace spasi dengan (`) agar bisa digunakan untuk explode
            $explode = explode("`", $replace); //memecah string berdasarkan (`)
            $selection = array_intersect($explode, $stopwords); // cari nilai yang sama antara explode dengan stopword


            if (count($selection) > (20 / 100) * count($explode)) { // dihitung apakah jumlah stopword (bhs inggris dalam string mencapai 20% atau tidak)
                $source = 'en';
                $target = 'en';
            } else {
                $source = 'in';
                $target = 'en';
            }

            $filter_string = preg_replace('/["]/', '', $string);
            $stringSource = $filter_string;

            $trans = new GoogleTranslate();
            $result = $trans->translate($source, $target, $stringSource);
            return $result;
        }

        function preprocessing($string, $stopwords)
        {
            // PREPROCESSING
            $lower = strtolower($string);
            $filtering1 = preg_replace('/[^a-z0-9 ]/', '', $lower); // filtering tahap 1 untuk menghilangkan simbol atau tanda baca. ex ("in the air") => (in air)
            $hasil_replace = preg_replace('/ /', ',', $filtering1); //replace spasi dengan koma agar bisa digunakan untuk explode
            $hasil_explode = explode(",", $hasil_replace); //memecah string berdasarkan koma
            $proses_stopwords = array_diff($hasil_explode, $stopwords); // mencari string pada $hasil_explode yang TIDAK ada pada $stopwords 
            $hasil_stopwords = implode(' ', $proses_stopwords);
            $filtering2 = preg_replace('/[^a-z0-9]/', '', $hasil_stopwords);
            return $filtering2;
        }

        function kgram($hasilPreprocessing, $kgram)
        {
            // K-GRAM
            $length = strlen($hasilPreprocessing); //mengambil panjan teks
            // dd($length);
            $teksSplit = array(); //variable tampung array
            if ($length < $kgram) {
                $teksSplit[] = $hasilPreprocessing; //jika panjang teks kurang dari nilai gram, maka semuanya langsung dimasukkan
            } else { //jika panjang teks lebih kecil dari nilai gram, maka masuk ke proses perulangan
                for ($i = 0; $i <= $length - $kgram; $i++) { //jika $i kurang dari samadengan panjang karakter - nilai gram.
                    $teksSplit[] = substr($hasilPreprocessing, $i, $kgram); //fungsi substr untuk memecah karakter. parameter $teks adalah teksnya, $i adalah index mulai pemecahan, $gram banyaknya pecahan karakter yang akan diambil
                }
            }
            return $teksSplit;
        }

        function rollinghash($hasilKgram, $base, $kgram)
        {
            // ROLLING HASH
            for ($i = 0; $i < count($hasilKgram); $i++) {
                $hasil_iterasi1 = array();
                if ($i === 0) {
                    $tampung = $hasilKgram[0];
                    for ($j = 0; $j < strlen($tampung); $j++) {
                        $iterasi1[] =  ord($tampung[$j]) * pow($base, $kgram - $j - 1); // menggunakan minus 1 karena index dihitung dari 0, sedangkan yang harus dimasukkan adalah urutan karakter
                    }
                    $hasil_iterasi1 = array_sum($iterasi1);
                    $nilai_hash[] = $hasil_iterasi1; // dipasang diluar karena proses hitung dilakukan setelah semua nilai didapat.
                } else if ($i !== 0) {
                    $ascii_pertama = ord($hasilKgram[$i - 1]);
                    // echo $ascii_pertama; die;
                    $huruf_terakhir = substr($hasilKgram[$i], -1);
                    // echo $huruf_terakhir; die;
                    $ascii_terakhir = ord($huruf_terakhir);
                    // echo $ascii_terakhir; die;
                    // echo end($nilai_hash); die;
                    $hasil_iterasi2 = (end($nilai_hash) - $ascii_pertama * pow($base, $kgram - 1)) * $base + $ascii_terakhir;
                    // echo $hasil_iterasi2; die;
                    $nilai_hash[] = $hasil_iterasi2;
                }
            }
            return $nilai_hash;
        }

        function window($hasilHashing, $wgram)
        {
            // WINDOW
            $length = count($hasilHashing);
            $hashingSplit = array();

            if ($length < $wgram) {
                $hashingSplit[] = $hasilHashing;
            } else {
                for ($k = 0; $k <= $length - $wgram; $k++) {
                    $hashingSplit[] = array_slice($hasilHashing, $k, $wgram); //parameternya: array, dari index, banyak element
                }
            }
            return $hashingSplit;
        }

        function fingerprint($wgram, $hasilWindow, $hasilHashing)
        {
            // FINGERPRINT
            $batas_pembanding = $wgram;
            $min_pos = array();
            $min = array();
            for ($i = 0; $i < count($hasilWindow); $i++) {

                $minimum = min($hasilWindow[$i]); //     mengambil nilai minimum dari tiap rangkaian window
                if (!in_array($minimum, $min)) {
                    $position = 0;
                    for ($k = 0; $k < count(array_slice($hasilHashing, 0, $batas_pembanding)); $k++) { // dilakukan perulangan dari index 0 sampai $batas_pembanding
                        if (array_slice($hasilHashing, 0, $batas_pembanding)[$k] <= $minimum) {
                            $position = $k; // nilai posisition akan di replace ditiap perulangan
                        }
                    }

                    $min_pos[] = $minimum . "," . $position; // menggabungkan nilai minimum dengan nilai posisi baru
                    $min[] = $minimum; // menggabungkan nilai minimum dengan nilai posisi baru
                }
                $batas_pembanding++;
            }
            // var_dump($minimum); die;
            // return array_unique($min_pos);
            return array('min_pos' => $min_pos, 'min' => $min);
        }



        // PROCESS AREA
        $kgram = 8;
        $wgram = 2;
        $base = 5;
        $get_stopword = stopword::select('data')->get()->toArray();
        $stopwords = array_pluck($get_stopword, 'data');

        // custom messages
        // $messages = [
        //     'document.max' => 'Exceeded upload limit, can only upload 15 data at a time'
        // ];

        // validasi input file NOTE: nilai min or max mengikuti tipe file. jika string, maka mengacu pada banyaknya karakter
        $request->validate([
            'document' => 'required',
            'document.*' => 'required'
        ]);



        // return $request->all();
        foreach ($request->document as $file) {
            $title[] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // ambil nama file
            $file_extension[]= $file->getClientOriginalExtension(); // ambil jenis ekstensi file
            $content[] = file_get_contents($file); // ambil content file
        }

        // Validasi manual jika dokumen bukan ekstensi txt
        foreach ($file_extension as $value) {
            if ($value !== 'txt') {
                return redirect('/corpus')->with('fail', 'The document must be a file of type: .txt');
                die;
            }
        }

        // Validasi manual jika banyak karakter dari dokumen melebihi 5000 karakter
        foreach ($content as $value) {
            if (strlen($value) >= 4923) {
                return redirect('/corpus')->with('fail', 'Document maximum number of characters exceeded: 4923');
                die;
            }
        }

        // running
        for ($i = 0; $i < count($request->document); $i++) {
            $hasilTranslate = translate($content[$i], $stopwords);
            $hasilPreprocessing = preprocessing($hasilTranslate, $stopwords);

            if ($hasilPreprocessing === "") {
                return redirect('/corpus')->with('error', "All text in the document is classified as stopword, so there is no text that can be processed further");
                die;
            }

            $hasilKgram = kgram($hasilPreprocessing, $kgram);
            $hasilRollinghash = rollinghash($hasilKgram, $base, $kgram);
            $hasilWindow = window($hasilRollinghash, $wgram);
            $hasilFingerprint = fingerprint($wgram, $hasilWindow, $hasilRollinghash);
            $hasilFingerprintMin[] = array_unique($hasilFingerprint['min']); // dimasukkan ke dalam database

        }

        // proses input data
        for ($i = 0; $i < count($request->document); $i++) {
            $hasilFingerprintMinImplode[] = implode(' ', $hasilFingerprintMin[$i]);
            // $hasilFingerprintMinExplode = explode(' ', $hasilFingerprintMinImplode);
            Document::create([
                'title' => $title[$i],
                'content' => $content[$i],
                'fingerprint' => $hasilFingerprintMinImplode[$i],
            ]);
        }
        return redirect('/corpus')->with('success', 'Data has been added successfully!'); // with untuk menambahkan pesan flash
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show($document) // parameter
    {
        // return $document;
        $document =  Document::find($document);
        return view('corpus.detail', compact('document'));
        // return view('corpus.detail', ['document' => $document]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy($document)
    {
        // return $document;
        Document::destroy($document);
        return redirect('/corpus')->with('success', 'Data successfully deleted!');
    }
}
