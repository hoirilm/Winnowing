<?php

namespace App\Http\Controllers;

use \Statickidz\GoogleTranslate; // untuk google translate
use App\stopword;
use Illuminate\Http\Request;


class AdvanceController extends Controller
{
    public function winnowing(Request $request)
    {
        function translate($string, $stopwords)
        {

            $replace = preg_replace('/ /', '`', $string); //replace spasi dengan (`) agar bisa digunakan untuk explode
            $explode = explode("`", $replace); //memecah string berdasarkan (`)
            $selection = array_intersect($explode, $stopwords); // cari nilai yang sama antara explode dengan stopword


            if (count($selection) > (20 / 100) * count($explode)) { // dihitung apakah jumlah stopword (bhs inggris dalam string mencapai 20% atau tidak)
                $source = 'en';
                $target = 'en';
                // $total = (20 / 100) * count($explode);
                // dd('total kata: ' . count($explode), 'batas seleksi: ' . $total, 'english', $selection);
            } else {
                $source = 'in';
                $target = 'en';
                // $total = (20 / 100) * count($explode);
                // dd('total kata: ' . count($explode), 'batas seleksi: ' . $total, 'indo', $selection);
            }

            $filter_string = preg_replace('/["]/', '', $string);  // menghilangkan tanda " karena mengganggu hasil terjemahan
            /* contoh => paste ke google translate
            "In this paper, we propose and evaluate a web-based software to check similarities of documents. The resemblance value of those documents will be compared based on the percentage of its word resemblance. The similarity value will help to detect plagiarism in documents. Methods used in this application are winnowing algorithm and web-based k-gram. We evaluate the accuracy of "the system" by comparing the system result with the human result. The differences between the systems and the respondents are 7% with k-gram 25 and 4% with k-gram 20. Moreover, processing time of our application are also discussed
            */
            $stringSource = $filter_string;

            $trans = new GoogleTranslate();
            $result = $trans->translate($source, $target, $stringSource);

            // $total = (20 / 100) * count($explode);
            // dd($source, $target, $string, $result, 'total kata: ' . count($explode), 'batas seleksi: ' . $total, 'english', $selection);
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
            $filtering2 = preg_replace('/ /', '', $hasil_stopwords); // filtering tapah 2 menghilangkan spasi
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
                // $hasil_iterasi1 = array();
                if ($i === 0) {
                    $tampung = $hasilKgram[0]; // mengambil nilai k-gram pertama
                    for ($j = 0; $j < strlen($tampung); $j++) { // perulangan sebanyak karakter pada kgram pertama
                        $iterasi1[] =  ord($tampung[$j]) * pow($base, $kgram - $j - 1); // menggunakan minus 1 karena index dihitung dari 0, sedangkan yang harus dimasukkan adalah urutan karakter
                    }
                    $hasil_iterasi1 = array_sum($iterasi1); // semua nilai yang tertampung di $iterasi1 dijumlahkan
                    $nilai_hash[] = $hasil_iterasi1; // dipasang diluar karena proses hitung dilakukan setelah semua nilai didapat.
                } else if ($i !== 0) {
                    $ascii_pertama = ord($hasilKgram[$i - 1]); // mengambil ascii pertama dari perhitungan sebelumnya.
                    // echo $ascii_pertama; die;
                    $huruf_terakhir = substr($hasilKgram[$i], -1); // mengambil huruf terakhir dari k-gram ke $i. parameter -1 untuk mengambil karakter terakhir
                    // echo $huruf_terakhir; die;
                    $ascii_terakhir = ord($huruf_terakhir); // convert $huruf_terakhir menjadi ascii
                    // echo $ascii_terakhir; die;
                    // echo end($nilai_hash); die;
                    $hasil_iterasi2 = (end($nilai_hash) - $ascii_pertama * pow($base, $kgram - 1)) * $base + $ascii_terakhir; // dilakukan perhitungan rumus iterasi ke 2 dst
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
            // contoh_hashing = array(713, 713, 713, 703, 703, 699, 732, 705, 746, 713, 758, 713); //10

            // contoh_window = [713, 713, 713], [713, 713, 703], [713, 703, 703], [703, 703, 699], [703, 699, 732], [699, 732, 705] dst
            
            $batas_pembanding = $wgram;
            $min_pos = array();
            $min = array();
            for ($i = 0; $i < count($hasilWindow); $i++) {
                $minimum = min($hasilWindow[$i]); // mengambil nilai minimum dari tiap rangkaian window
                if (!in_array($minimum, $min)) { // cek apakah nilai minimum yang didapat sudah ada di array min atau tidak

                    // PENENTUAN POSISI ========================== dilakukan saat nilai minumum tidak ada di array

                    $position = 0;
                    for ($k = 0; $k < count(array_slice($hasilHashing, 0, $batas_pembanding)); $k++) {
                        // for ($k = 0; $k < $batas_pembanding; $k++) { // dilakukan perulangan dari index 0 sampai $batas_pembanding / banyaknya nilai wgram
                        if (array_slice($hasilHashing, 0, $batas_pembanding)[$k] <= $minimum) {
                            // if ($hasilHashing[$k] <= $minimum) { // jika nilai hashing index ke $k <= nilai minimum,
                            $position = $k; // maka nilai posisition akan di replace ditiap perulangan
                        }
                    } // akan terus looping sampai dengan batas pembanding

                    // END PENENTUAN POSISI =======================

                    $min_pos[] = $minimum . "," . $position; // menggabungkan nilai minimum dengan nilai posisi baru
                    $min[] = $minimum; // menggabungkan nilai minimum dengan nilai posisi baru
                }
                $batas_pembanding++; // ditambah untuk memperluas skala pencarian position di nilai hash 
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

        // $stopwords = stopword::all()
        $get_stopword = stopword::select('data')->get()->toArray();
        $stopwords = array_pluck($get_stopword, 'data');
        // var_dump($stopwords); die;

        // VALIDASI
        $request->validate([
            'source' => 'required|max:5000',
            'target' => 'required|max:5000',
            'kgram' => 'required|integer|min:1',
            'wgram' => 'required|integer|min:1',
            'base' => 'required|integer|min:1'
        ]);

        // AMBIL REQUEST
        $source = $request->source;
        $target = $request->target;
        $kgram = $request->kgram;
        $wgram = $request->wgram;
        $base = $request->base;

        // ARRAY TAMPUNG UNTUK FINGERPRINT
        // $hasilFingerprintSource = array();
        // $hasilFingerprintTarget = array();


        $i = 1;
        // $hasilFingerprintSourceMinPos = array();
        // $hasilFingerprintTargetMinPos = array();
        $hasilFingerprintSourceMin = array();
        $hasilFingerprintTargetMin = array();
        while ($i <= 2) {
            if ($i === 1) {
                $hasilTranslate1 = translate($source, $stopwords);
                $hasilPreprocessing1 = preprocessing($hasilTranslate1, $stopwords);
                // dd($hasilPreprocessing1);

                if ($hasilPreprocessing1 === "") {
                    return redirect('/advance')->with('error', "All text you've entered is classified as stopword, so no text can be processed further");
                    die;
                }

                $hasilKgram1 = kgram($hasilPreprocessing1, $kgram);
                $hasilHashing1 = rollinghash($hasilKgram1, $base, $kgram);
                $hasilWindow1 = window($hasilHashing1, $wgram);
                $hasilFingerprintSource = fingerprint($wgram, $hasilWindow1, $hasilHashing1);
                // dd($hasilTranslate1, $hasilPreprocessing1, $hasilKgram1, $hasilHashing1, $hasilWindow1,$hasilFingerprintSource);



                // $hasilFingerprintSourceMin = array_unique($hasilFingerprintSource['min']); // mengambil data minimum saja (untuk dihitung)
                // $hasilFingerprintSourceMinPos = array_unique($hasilFingerprintSource['min_pos']); // mengambil data minimum dan posisi (untuk ditampilkan)



                $hasilFingerprintSourceMin = $hasilFingerprintSource['min']; // mengambil data minimum saja (untuk dihitung)
                $hasilFingerprintSourceMinPos = $hasilFingerprintSource['min_pos']; // mengambil data minimum dan posisi (untuk ditampilkan)
                // print_r($hasilFingerprintSourceMinPos); die;
            } else if ($i === 2) {
                $hasilTranslate2 = translate($target, $stopwords);
                $hasilPreprocessing2 = preprocessing($hasilTranslate2, $stopwords);

                if ($hasilPreprocessing2 === "") {
                    return redirect('/advance')->with('error',  "All text you've entered is classified as stopword, so no text can be processed further");
                    die;
                }

                $hasilKgram2 = kgram($hasilPreprocessing2, $kgram);
                $hasilHashing2 = rollinghash($hasilKgram2, $base, $kgram);
                $hasilWindow2 = window($hasilHashing2, $wgram);
                $hasilFingerprintTarget = fingerprint($wgram, $hasilWindow2, $hasilHashing2);



                // $hasilFingerprintTargetMin = array_unique($hasilFingerprintTarget['min']); // mengambil data minimum saja (untuk dihitung)
                // $hasilFingerprintTargetMinPos = array_unique($hasilFingerprintTarget['min_pos']); // mengambil data minimum dan posisi (untuk ditampilkan)


                $hasilFingerprintTargetMin = $hasilFingerprintTarget['min']; // mengambil data minimum saja (untuk dihitung)
                $hasilFingerprintTargetMinPos = $hasilFingerprintTarget['min_pos']; // mengambil data minimum dan posisi (untuk ditampilkan)
                // print_r($hasilFingerprintTargetMinPos); die;
            }
            $i++;
        }
        $jaccard = jaccard($hasilFingerprintSourceMin, $hasilFingerprintTargetMin); // mengambil nilai fingeprint tanpa posisi
        // $jaccard = jaccard($hasilFingerprintSourceMinPos, $hasilFingerprintTargetMinPos); // mengambil nilai fingeprint dengan posisi

        return view(
            'advance.result',
            [
                'jaccard1' => $jaccard['hasil1'],
                'jaccard2' => $jaccard['hasil2'],
                'jaccard3' => $jaccard['hasil3'],
                'jaccard4' => $jaccard['hasil4'],
                'source' => $source,
                'target' => $target,
                'kgram' => $kgram,
                'wgram' => $wgram,
                'translate1' => $hasilTranslate1,
                'translate2' => $hasilTranslate2,
                'preprocessing1' => $hasilPreprocessing1,
                'preprocessing2' => $hasilPreprocessing2,
                'kgram1' => $hasilKgram1,
                'kgram2' => $hasilKgram2,
                'hashing1' => $hasilHashing1,
                'hashing2' => $hasilHashing2,
                'window1' => $hasilWindow1,
                'window2' => $hasilWindow2,
                'fingerprint1' => $hasilFingerprintSourceMinPos,
                'fingerprint2' => $hasilFingerprintTargetMinPos //mengambil nilai fingerprint dengan posisi untuk ditampilkan sebagai info
            ]
        );
    }
}
