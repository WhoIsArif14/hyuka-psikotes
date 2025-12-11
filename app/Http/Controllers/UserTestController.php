<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule;
use App\Models\PapiResult;
use App\Models\RmibResult; // Diperlukan untuk cek status RMIB
use App\Models\AlatTes;    // Diperlukan untuk Route Model Binding dan Helpers
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class UserTestController extends Controller
{
    /**
     * Route helper: start tanpa parameter.
     */
    public function start()
    {
        $activeTestId = Session::get('active_test_id');

        if (!$activeTestId) {
            return redirect()->route('login')->with('error', 'Tidak ada sesi tes aktif. Silakan masukkan kode tes terlebih dahulu.');
        }

        $test = Test::find($activeTestId);

        if (!$test) {
            Session::forget(['active_test_id', 'participant_data', 'accessed_test_code']);
            return redirect()->route('login')->with('error', 'Tes tidak ditemukan. Silakan masukkan kode tes lagi.');
        }

        return $this->startTest($test);
    }

    /**
     * Menganalisis Modul Tes dan mengarahkan ke controller Alat Tes yang sesuai
     * berdasarkan urutan ($test->test_order) dan status penyelesaian user.
     */
    public function startTest(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan masukkan kode tes terlebih dahulu.');
        }

        $test->load('alatTes');
        // Gunakan test_order yang diatur admin, atau default ke urutan relasi
        $orderedAlatTesIds = $test->test_order ?? $test->alatTes->pluck('id')->toArray();
        $userId = auth()->id();

        // 1. Temukan Alat Tes berikutnya yang harus dikerjakan
        $nextAlatTes = $this->getNextAlatTes($test, $orderedAlatTesIds, $userId);

        // Jika tidak ada lagi alat tes yang harus dikerjakan
        if (!$nextAlatTes) {
            // Hapus sesi aktif tes setelah semua alat tes selesai
            Session::forget(['accessed_test_code', 'participant_data', 'active_test_id']);
            return redirect()->route('tests.module.finish', $test->id)
                ->with('success', 'Selamat, Anda telah menyelesaikan semua alat tes dalam modul ini!');
        }
        
        // 2. Arahkan ke Controller Alat Tes yang sesuai
        $slug = strtolower($nextAlatTes->slug ?? $nextAlatTes->name ?? '');

        if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
            $papiController = new \App\Http\Controllers\PapiTestController();
            return $papiController->showTestForm($test, $nextAlatTes); 
        }

        if (str_contains($slug, 'rmib')) {
            $rmibController = new \App\Http\Controllers\RmibTestController();
            return $rmibController->showTestForm($test, $nextAlatTes);
        }
        
        // 3. Jika Alat Tes Umum (Pilihan Ganda, Soal Biasa)
        if ($nextAlatTes->questions->isNotEmpty()) {
            // Set ID Alat Tes saat ini ke sesi
            Session::forget('current_alat_tes_id');
            Session::put('current_alat_tes_id', $nextAlatTes->id); 
            
            // Set waktu mulai untuk Alat Tes ini
            if (!Session::has('test_start_time_' . $nextAlatTes->id)) {
                Session::put('test_start_time_' . $nextAlatTes->id, now());
            }

            // Redirect ke soal pertama Alat Tes ini
            return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $nextAlatTes->id, 'number' => 1]);
        }
        
        // Kasus: Alat Tes tidak dikenali atau tidak memiliki soal
        return redirect()->route('login')
             ->with('error', 'Alat tes "' . $nextAlatTes->name . '" tidak dapat diproses. Hubungi administrator.');
    }
    
    /**
     * Helper untuk mendapatkan Alat Tes berikutnya yang belum selesai.
     */
    protected function getNextAlatTes(Test $test, array $orderedAlatTesIds, int $userId)
    {
        $alatTesCollection = $test->alatTes->keyBy('id');
        
        foreach ($orderedAlatTesIds as $alatTesId) {
            $alatTes = $alatTesCollection->get($alatTesId);

            if (!$alatTes) continue;

            $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
            $isFinished = false;

            // Logika pengecekan status penyelesaian
            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                $isFinished = PapiResult::where('user_id', $userId)->exists();
            } elseif (str_contains($slug, 'rmib')) {
                $isFinished = RmibResult::where('user_id', $userId)
                                        ->where('alat_tes_id', $alatTesId)->exists();
            } else {
                // Untuk Alat Tes Umum, cek di TestResult
                // Catatan: Pastikan TestResult memiliki user_id dan alat_tes_id
                $isFinished = TestResult::where('user_id', $userId)
                                        ->where('alat_tes_id', $alatTesId)
                                        ->exists();
            }

            if (!$isFinished) {
                return $alatTes;
            }
        }

        return null;
    }


    // --- LOGIKA UNTUK TES UMUM (NON-PAPI/RMIB) ---

    /**
     * Menampilkan soal berdasarkan nomor urut (Hanya untuk soal umum)
     */
    public function showQuestion(Test $test, AlatTes $alatTes, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }
        
        // Pastikan alat tes ini adalah bagian dari tes yang aktif
        if ($test->AlatTes()->where('alat_tes.id', $alatTes->id)->doesntExist()) {
             return redirect()->route('tests.start')->with('error', 'Alat tes ini bukan bagian dari modul yang aktif.');
        }

        $alatTes->load('questions');
        $questions = $alatTes->questions;
        $totalQuestions = $questions->count();

        // Validasi nomor soal
        if ($number < 1 || $number > $totalQuestions) {
             return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => 1]);
        }

        $currentQuestion = $questions[$number - 1];
        
        // Ambil jawaban yang sudah disimpan (menggunakan ID AlatTes sebagai kunci sesi)
        $savedAnswers = Session::get('test_answers_' . $alatTes->id, []);
        $savedAnswer = $savedAnswers[$currentQuestion->id] ?? null;

        // Cek waktu tersisa (Menggunakan ID AlatTes sebagai kunci waktu)
        $startTime = Session::get('test_start_time_' . $alatTes->id);
        
        // Asumsi: Kita menggunakan durasi AlatTes jika durasi modul diabaikan/nol
        $timeLimit = $alatTes->duration_minutes * 60; // Asumsi ada kolom duration_minutes di AlatTes
        $timeElapsed = now()->diffInSeconds($startTime);
        $timeRemaining = max(0, $timeLimit - $timeElapsed);
        
        // Jika waktu habis, paksa submit
        if ($timeRemaining === 0) {
             return $this->submitAlatTes($test, $alatTes);
        }
        
        return view('test-single', [
            'test' => $test,
            'alatTes' => $alatTes,
            'question' => $currentQuestion,
            'currentNumber' => $number,
            'totalQuestions' => $totalQuestions,
            'savedAnswer' => $savedAnswer,
            'timeRemaining' => $timeRemaining
        ]);
    }
    
    /**
     * Menyimpan jawaban sementara dan navigasi (Non-PAPI/RMIB)
     */
    public function saveAnswer(Request $request, Test $test, AlatTes $alatTes, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $alatTes->load('questions');
        $questions = $alatTes->questions;
        $currentQuestion = $questions[$number - 1];

        // Simpan jawaban ke session (menggunakan ID AlatTes sebagai kunci)
        $answers = Session::get('test_answers_' . $alatTes->id, []);
        
        if ($request->has('answer')) {
            $answers[$currentQuestion->id] = $request->input('answer');
        }
        
        Session::put('test_answers_' . $alatTes->id, $answers);

        // Navigasi
        $action = $request->input('action');
        
        if ($action === 'previous' && $number > 1) {
            return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => $number - 1]);
        }
        
        if ($action === 'next' && $number < $questions->count()) {
            return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => $number + 1]);
        }
        
        if ($action === 'submit') {
            return $this->submitAlatTes($test, $alatTes);
        }

        return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => $number]);
    }

    /**
     * Submit Alat Tes Umum (Non-PAPI/RMIB)
     */
    private function submitAlatTes(Test $test, AlatTes $alatTes)
    {
        $alatTes->load('questions');
        $questions = $alatTes->questions;
        $savedAnswers = Session::get('test_answers_' . $alatTes->id, []);
        $participantData = Session::get('participant_data', []);
        
        $score = 0;
        $selectedOptionIds = [];
        
        foreach ($questions as $question) {
            if (isset($savedAnswers[$question->id])) {
                $optionIndex = $savedAnswers[$question->id];
                
                // Parse options
                $options = is_string($question->options) 
                    ? json_decode($question->options, true) 
                    : $question->options ?? [];
                
                if (isset($options[$optionIndex])) {
                    $point = $options[$optionIndex]['point'] ?? 0;
                    $score += $point;
                    $selectedOptionIds[$question->id] = $optionIndex;
                }
            }
        }

        // Simpan ke database
        $startTime = Session::get('test_start_time_' . $alatTes->id, now());
        
        $testResult = TestResult::create([
            'test_id' => $test->id,
            'alat_tes_id' => $alatTes->id, 
            'user_id' => auth()->id(), 
            'score' => $score,
            'start_time' => $startTime,
            'end_time' => now(),
            // Kolom dari sesi participant_data
            'participant_name' => $participantData['participant_name'] ?? null,
            'participant_email' => $participantData['participant_email'] ?? null,
            'phone_number' => $participantData['phone_number'] ?? null,
            'education' => $participantData['education'] ?? null,
            'major' => $participantData['major'] ?? null,
        ]);

        // Simpan user answers
        $userAnswers = [];
        foreach ($selectedOptionIds as $question_id => $option_index) {
            // Asumsi relasi userAnswers di TestResult model adalah HasMany
            $userAnswers[] = [
                'test_result_id' => $testResult->id,
                'question_id' => $question_id,
                'option_id' => $option_index, // Ini menyimpan index opsi, bukan ID Opsi
            ];
        }
        
        // Simpan jawaban jika ada
        if (!empty($userAnswers)) {
             // Pastikan relasi userAnswers di TestResult model ada dan benar
             // $testResult->userAnswers()->createMany($userAnswers); 
             // Jika Model UserAnswer tidak ada, gunakan DB::table()->insert() atau buat modelnya.
             // Asumsi: Model UserAnswer ada.
        }

        // Clear session untuk Alat Tes yang baru selesai
        Session::forget('test_answers_' . $alatTes->id);
        Session::forget('test_start_time_' . $alatTes->id);
        Session::forget('current_alat_tes_id');

        // Redirect kembali ke startTest() untuk memuat Alat Tes berikutnya
        return redirect()->route('tests.start')->with('success', 'Tes ' . $alatTes->name . ' selesai. Memuat tes berikutnya...');
    }
    
    /**
     * Menampilkan halaman selesai modul
     */
    public function finishModule(Test $test)
    {
        // View yang menampilkan ucapan selamat dan instruksi selanjutnya
        return view('test-module-finish', compact('test'));
    }

    // --- METHOD LEGACY DIHAPUS/DIBAWAH INI TIDAK DIPERLUKAN LAGI ---

    public function show(Test $test)
    {
        return redirect()->route('tests.start');
    }

    public function store(Request $request, Test $test)
    {
        // Method ini tidak digunakan lagi karena submission dilakukan per Alat Tes
        return redirect()->route('tests.start')->with('error', 'Submission gagal, gunakan alur Alat Tes.');
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