<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule;
use App\Models\PapiResult; // Import Model PAPI untuk pengecekan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class UserTestController extends Controller
{
    /**
     * Route helper: start tanpa parameter.
     * Akan digunakan ketika route dipanggil tanpa memberikan {test}.
     * Fungsi ini mengambil active_test_id dari session dan memanggil startTest().
     */
    public function start()
    {
        $activeTestId = Session::get('active_test_id');

        if (!$activeTestId) {
            // Tidak ada sesi aktif: arahkan kembali ke form login/data diri
            return redirect()->route('login')->with('error', 'Tidak ada sesi tes aktif. Silakan masukkan kode tes terlebih dahulu.');
        }

        $test = Test::find($activeTestId);

        if (!$test) {
            // Jika id test di session tidak valid, bersihkan session dan minta login ulang
            Session::forget(['active_test_id', 'participant_data', 'accessed_test_code']);
            return redirect()->route('login')->with('error', 'Tes tidak ditemukan. Silakan masukkan kode tes lagi.');
        }

        // Panggil method utama yang menangani logika berdasarkan jenis alat tes
        return $this->startTest($test);
    }

    /**
     * Metode baru: Menganalisis Modul Tes yang dipilih dan mengalihkan ke Controller yang tepat.
     * Ini akan menjadi titik masuk utama (Entry Point) untuk memulai tes.
     */
    public function startTest(Test $test)
    {
        // 1. Verifikasi Sesi
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan masukkan kode tes terlebih dahulu.');
        }

        // 2. Cek Jenis Alat Tes (PAPI Kostick memiliki penanganan khusus)
        // Asumsi: Modul Tes memiliki relasi AlatTes, dan AlatTes memiliki kolom 'slug'.
        $alatTes = $test->AlatTes->first(); 
        
        if ($alatTes && $alatTes->slug === 'papi-kostick') {
            
            // Cek apakah peserta sudah pernah menyelesaikan PAPI untuk mencegah tes ganda
            if (PapiResult::where('user_id', auth()->id())->exists()) {
                 return redirect()->route('tests.result.status')->with('error', 'Anda sudah menyelesaikan Tes PAPI Kostick.');
            }
            
            // PENGALIHAN KRUSIAL: Panggil Controller yang memuat form PAPI
            $papiController = new PapiTestController();
            return $papiController->showTestForm(); 
            
        }

        // 3. Jika bukan PAPI, lanjutkan ke metode show() yang lama (Tes Umum)
        return $this->show($test);
    }

    /**
     * Menampilkan halaman pengerjaan tes umum (Hanya dipanggil jika BUKAN PAPI).
     */
    public function show(Test $test)
    {
        // Note: Verifikasi Sesi sudah dilakukan di startTest(), 
        // tetapi kita tinggalkan untuk keamanan jika show() dipanggil langsung.
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }
        
        // Memuat soal tes umum
        $test->load('questions.options');
        return view('test', compact('test'));
    }

    /**
     * Menyimpan hasil tes umum (Hanya dipanggil jika BUKAN PAPI).
     */
    public function store(Request $request, Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $request->validate(['questions' => ['required', 'array']]);
        $participantData = Session::get('participant_data', []);
        
        // Logika Penskoran Tes Umum (Pilihan Ganda/Point Based)
        $score = 0;
        $selectedOptionIds = $request->input('questions');
        $options = Option::findMany(array_values($selectedOptionIds));
        foreach ($options as $option) {
            $score += $option->point;
        }

        $testResult = TestResult::create([
            'test_id' => $test->id,
            'score' => $score,
            'start_time' => now(),
            'end_time' => now(),
            // ... (data peserta lainnya) ...
            'participant_name' => $participantData['participant_name'] ?? null,
            'participant_email' => $participantData['participant_email'] ?? null,
            'phone_number' => $participantData['phone_number'] ?? null,
            'education' => $participantData['education'] ?? null,
            'major' => $participantData['major'] ?? null,
        ]);

        $userAnswers = [];
        foreach ($selectedOptionIds as $question_id => $option_id) {
            $userAnswers[] = [
                'test_result_id' => $testResult->id,
                'question_id' => $question_id,
                'option_id' => $option_id,
            ];
        }
        $testResult->userAnswers()->createMany($userAnswers);

        Session::forget(['accessed_test_code', 'participant_data', 'active_test_id']);

        return redirect()->route('tests.result', $testResult);
    }

    /**
     * Menampilkan halaman hasil (status selesai).
     */
    public function result(TestResult $testResult)
    {
        $testResult->load('test');
        return view('results', compact('testResult'));
    }
}
