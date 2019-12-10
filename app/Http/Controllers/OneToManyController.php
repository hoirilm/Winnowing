<?php

namespace App\Http\Controllers;

use App\Document;
use App\stopword;
use \Statickidz\GoogleTranslate; // untuk google translate
use Illuminate\Http\Request;

class OneToManyController extends Controller
{
    public function winnowing(Request $request)
    {

        // METHOD AREA
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

        function jaccard($hasilFingerprintSource, $hasilFingerprintTarget)
        {
            // JACCARD
            $hasil1 = array_intersect($hasilFingerprintSource, $hasilFingerprintTarget); //irisan "n"
            $hasil2 = array_merge($hasilFingerprintSource, $hasilFingerprintTarget); //gabungan "u"
            $hasil3 = count($hasil2) - count($hasil1);
            $hasil4 = (count($hasil1) / $hasil3) * 100;

            return array('hasil1' => $hasil1, 'hasil2' => $hasil2, 'hasil3' => $hasil3, 'hasil4' => $hasil4);
        }
        // END METHOD AREA





        // PROCESS AREA
        $kgram = 8;
        $wgram = 2;
        $base = 5;
        $get_stopword = stopword::select('data')->get()->toArray();
        $stopwords = array_pluck($get_stopword, 'data');

        // validasi input file NOTE: nilai min or max mengikuti tipe file. jika string, maka mengacu pada banyaknya karakter
        $request->validate([
            'document' => 'required',
            'document.*' => 'required'
        ]);


        // MENGAMBIL INFO FILE
        // return $request->all();
        $file = $request->document;
        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // ambil nama file
        $file_extension = $file->getClientOriginalExtension(); // ambil jenis ekstensi file
        $content = file_get_contents($file); // ambil content file


        // Validasi manual jika dokumen bukan ekstensi txt
        if ($file_extension !== 'txt') {
            return redirect('/onetomany')->with('fail', 'The document must be a file of type: .txt');
            die;
        }

        // Validasi manual jika banyak karakter dari dokumen melebihi 5000 karakter
        if (strlen($content) >= 5000) {
            return redirect('/onetomany')->with('fail', 'Document maximum number of characters exceeded: 4900');
            die;
        }



        // running
        $hasilTranslate = translate($content, $stopwords);
        $hasilPreprocessing = preprocessing($hasilTranslate, $stopwords);
        if ($hasilPreprocessing === "") {
            return redirect('/onetomany')->with('error', "All text in the document is classified as stopword, so there is no text that can be processed further");
            die;
        }
        $hasilKgram = kgram($hasilPreprocessing, $kgram);
        $hasilRollinghash = rollinghash($hasilKgram, $base, $kgram);
        $hasilWindow = window($hasilRollinghash, $wgram);
        $hasilFingerprint = fingerprint($wgram, $hasilWindow, $hasilRollinghash);
        $hasilFingerprintSourceMin[] = $hasilFingerprint['min']; // yang akan diambil untuk dibandingkan dengan corpus



        // pengambilan fingerprint corpus
        $corpus = Document::all(); // ambil semua data Documents
        // dd(count($corpus));
        if (count($corpus) === 0) {
            return redirect('onetomany')->with('corpus', 'No corpus found!');
        } else {
            for ($i = 0; $i < count($corpus); $i++) {
                // $stdclass_to_array = json_decode(json_encode(), true); // convert stdclass ke array untuk data fingerprint corpus
                $string_to_array = explode(' ', $corpus[$i]['fingerprint']); // convert array ke string
                $hasilFingerprintTargetMin[] = $string_to_array;
                $jaccard[] = jaccard($hasilFingerprintSourceMin[0], $hasilFingerprintTargetMin[$i]);
                // var_dump(number_format($jaccard[$i]['hasil4'], 2));

                $data[] = [
                    'fileUploadTitle' => $title,
                    'fileUploadContent' => $content,
                    'corpusId' => $corpus[$i]['id'],
                    'corpusTitle' => $corpus[$i]['title'],
                    'corpusContent' => $corpus[$i]['content'],
                    'similarity' => $jaccard[$i]['hasil4']
                ];
            }

            // sorting data berdasarkan similarity
            usort($data, function ($a, $b) {
                $c = $b['similarity'] > $a['similarity'];
                return $c;
            });

            // return $data;

            return view('oneToMany.result', ['collection' => $data]);
        }
    }





    // DETAIL
    public function detail(Request $request)
    {

        // METHOD AREA
        function translatedetail($string, $stopwords)
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

        function preprocessingdetail($string, $stopwords)
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

        function kgramdetail($hasilPreprocessing, $kgram)
        {
            // K-GRAM
            $i = 0; //inisiasi nilai $i = 0
            $length = strlen($hasilPreprocessing); //mengambil panjan teks
            // dd($length);
            $teksSplit = array(); //variable tampung array
            if (strlen($hasilPreprocessing) < $kgram) {
                $teksSplit[] = $hasilPreprocessing; //jika panjang teks kurang dari nilai gram, maka semuanya langsung dimasukkan
            } else { //jika panjang teks lebih kecil dari nilai gram, maka masuk ke proses perulangan
                for ($i; $i <= $length - $kgram; $i++) { //jika $i kurang dari samadengan panjang karakter - nilai gram.
                    $teksSplit[] = substr($hasilPreprocessing, $i, $kgram); //fungsi substr untuk memecah karakter. parameter $teks adalah teksnya, $i adalah index mulai pemecahan, $gram banyaknya pecahan karakter yang akan diambil
                }
            }
            return $teksSplit;
        }

        function rollinghashdetail($hasilKgram, $base, $kgram)
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

        function windowdetail($hasilHashing, $wgram)
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

        function fingerprintdetail($wgram, $hasilWindow, $hasilHashing)
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

        function jaccarddetail($hasilFingerprintSource, $hasilFingerprintTarget)
        {
            // JACCARD
            $hasil1 = array_intersect($hasilFingerprintSource, $hasilFingerprintTarget); //irisan "n"
            $hasil2 = array_merge($hasilFingerprintSource, $hasilFingerprintTarget); //gabungan "u"
            $hasil3 = count($hasil2) - count($hasil1);
            $hasil4 = (count($hasil1) / $hasil3) * 100;

            return array('hasil1' => $hasil1, 'hasil2' => $hasil2, 'hasil3' => $hasil3, 'hasil4' => $hasil4);
        }
        // END METHOD AREA




        // ambil data dari hasil one to many
        $contentTitle = $request->fileUploadTitle;
        $contentUpload = $request->fileUploadContent;
        $corpusId = $request->corpusId;

        // ambil stopwords
        $get_stopword = stopword::select('data')->get()->toArray();
        $stopwords = array_pluck($get_stopword, 'data');
        $kgram = 8;
        $wgram = 2;
        $base = 5;

        // running winnowing untuk file upload
        $hasilTranslateFileUpload = translatedetail($contentUpload, $stopwords);
        $hasilPreprocessingFileUpload = preprocessingdetail($hasilTranslateFileUpload, $stopwords);
        $hasilKgramFileUpload = kgramdetail($hasilPreprocessingFileUpload, $kgram);
        $hasilRollinghashFileUpload = rollinghashdetail($hasilKgramFileUpload, $base, $kgram);
        $hasilWindowFileUpload = windowdetail($hasilRollinghashFileUpload, $wgram);
        $hasilFingerprintFileUpload = fingerprintdetail($wgram, $hasilWindowFileUpload, $hasilRollinghashFileUpload);
        $hasilFingerprintSourceMinFileUpload = $hasilFingerprintFileUpload['min']; // yang akan diambil untuk dibandingkan dengan corpus
        // $hasilFingerprintSourceMinPosFileUpload = $hasilFingerprintFileUpload['min_pos'];

        // proses pengambilan dokumen corpus
        $corpus = Document::find($corpusId);

        // running winnowing untuk file corpus
        $hasilTranslateCorpus = translatedetail($corpus->content, $stopwords);
        $hasilPreprocessingCorpus = preprocessingdetail($hasilTranslateCorpus, $stopwords);
        $hasilKgramCorpus = kgramdetail($hasilPreprocessingCorpus, $kgram);
        $hasilRollinghashCorpus = rollinghashdetail($hasilKgramCorpus, $base, $kgram);
        $hasilWindowCorpus = windowdetail($hasilRollinghashCorpus, $wgram);
        $string_to_array = explode(' ', $corpus->fingerprint); // convert array ke string
        $fingerprintCorpus = $string_to_array;

        $jaccard = jaccarddetail($hasilFingerprintSourceMinFileUpload, $fingerprintCorpus);
        // dd($jaccard);

        return view(
            'oneToMany.detail',
            [
                'judul1' => $contentTitle,
                'judul2' => $corpus->title,
                'jaccard1' => $jaccard['hasil1'],
                'jaccard2' => $jaccard['hasil2'],
                'jaccard3' => $jaccard['hasil3'],
                'jaccard4' => $jaccard['hasil4'],
                'source' => $contentUpload,
                'target' => $corpus->content,
                'kgram' => $kgram,
                'wgram' => $wgram,
                'translate1' => $hasilTranslateFileUpload,
                'translate2' => $hasilTranslateCorpus,
                'preprocessing1' => $hasilPreprocessingFileUpload,
                'preprocessing2' => $hasilPreprocessingCorpus,
                'kgram1' => $hasilKgramFileUpload,
                'kgram2' => $hasilKgramCorpus,
                'hashing1' => $hasilRollinghashFileUpload,
                'hashing2' => $hasilRollinghashCorpus,
                'window1' => $hasilWindowFileUpload,
                'window2' => $hasilWindowCorpus,
                // 'fingerprint1' => $hasilFingerprintSourceMinPosFileUpload, // mengirim nilai minimal + index
                'fingerprint1' => $hasilFingerprintSourceMinFileUpload, // mengirim nilai minimal
                'fingerprint2' => $fingerprintCorpus //mengambil nilai fingerprint dengan posisi untuk ditampilkan sebagai info
            ]
        );
    }
}
