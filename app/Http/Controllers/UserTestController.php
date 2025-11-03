<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule;
use App\Models\PapiResult;
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
     * Menganalisis Modul Tes dan mengarahkan ke controller yang sesuai.
     */
    public function startTest(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan masukkan kode tes terlebih dahulu.');
        }

        $test->load('alatTes');
        $alatTes = $test->alatTes->firstWhere(fn($alat) => $alat->questions->isNotEmpty());

        if (!$alatTes) {
            return redirect()->route('login')
                ->with('error', 'Alat tes tidak ditemukan atau belum memiliki soal. Hubungi administrator.');
        }

        $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
        $isPapi = in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick']);

        if ($isPapi) {
            if (PapiResult::where('user_id', auth()->id())->exists()) {
                return redirect()->route('tests.result.status')
                    ->with('error', 'Anda sudah menyelesaikan Tes PAPI Kostick.');
            }

            $papiController = new \App\Http\Controllers\PapiTestController();
            return $papiController->showTestForm($test);
        }

        // Reset session jawaban jika mulai tes baru
        Session::forget('test_answers_' . $test->id);
        Session::forget('test_start_time_' . $test->id);
        Session::put('test_start_time_' . $test->id, now());

        // Redirect ke soal pertama
        return redirect()->route('tests.question', ['test' => $test->id, 'number' => 1]);
    }

    /**
     * Menampilkan soal berdasarkan nomor urut
     */
    public function showQuestion(Test $test, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        $test->load('alatTes.questions');
        $alatTes = $test->alatTes->firstWhere(fn($alat) => $alat->questions->isNotEmpty());

        if (!$alatTes) {
            return redirect()->route('login')
                ->with('error', 'Belum ada soal untuk tes ini. Hubungi administrator.');
        }

        $questions = $alatTes->questions;
        $totalQuestions = $questions->count();

        // Validasi nomor soal
        if ($number < 1 || $number > $totalQuestions) {
            return redirect()->route('tests.question', ['test' => $test->id, 'number' => 1]);
        }

        $currentQuestion = $questions[$number - 1];
        
        // Ambil jawaban yang sudah disimpan
        $savedAnswers = Session::get('test_answers_' . $test->id, []);
        $savedAnswer = $savedAnswers[$currentQuestion->id] ?? null;

        // Cek waktu tersisa
        $startTime = Session::get('test_start_time_' . $test->id);
        $timeElapsed = now()->diffInSeconds($startTime);
        $timeLimit = $test->duration_minutes * 60;
        $timeRemaining = max(0, $timeLimit - $timeElapsed);

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
     * Menyimpan jawaban sementara dan navigasi
     */
    public function saveAnswer(Request $request, Test $test, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $test->load('alatTes.questions');
        $alatTes = $test->alatTes->firstWhere(fn($alat) => $alat->questions->isNotEmpty());
        $questions = $alatTes->questions;
        $currentQuestion = $questions[$number - 1];

        // Simpan jawaban ke session
        $answers = Session::get('test_answers_' . $test->id, []);
        
        if ($request->has('answer')) {
            $answers[$currentQuestion->id] = $request->input('answer');
        }
        
        Session::put('test_answers_' . $test->id, $answers);

        // Navigasi
        $action = $request->input('action');
        
        if ($action === 'previous' && $number > 1) {
            return redirect()->route('tests.question', ['test' => $test->id, 'number' => $number - 1]);
        }
        
        if ($action === 'next' && $number < $questions->count()) {
            return redirect()->route('tests.question', ['test' => $test->id, 'number' => $number + 1]);
        }
        
        if ($action === 'submit') {
            return $this->submitTest($test);
        }

        return redirect()->route('tests.question', ['test' => $test->id, 'number' => $number]);
    }

    /**
     * Submit final test
     */
    private function submitTest(Test $test)
    {
        $test->load('alatTes.questions');
        $alatTes = $test->alatTes->firstWhere(fn($alat) => $alat->questions->isNotEmpty());
        $questions = $alatTes->questions;
        
        $savedAnswers = Session::get('test_answers_' . $test->id, []);
        $participantData = Session::get('participant_data', []);

        // Hitung score
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
        $startTime = Session::get('test_start_time_' . $test->id, now());
        
        $testResult = TestResult::create([
            'test_id' => $test->id,
            'score' => $score,
            'start_time' => $startTime,
            'end_time' => now(),
            'participant_name' => $participantData['participant_name'] ?? null,
            'participant_email' => $participantData['participant_email'] ?? null,
            'phone_number' => $participantData['phone_number'] ?? null,
            'education' => $participantData['education'] ?? null,
            'major' => $participantData['major'] ?? null,
        ]);

        // Simpan user answers
        $userAnswers = [];
        foreach ($selectedOptionIds as $question_id => $option_index) {
            $userAnswers[] = [
                'test_result_id' => $testResult->id,
                'question_id' => $question_id,
                'option_id' => $option_index,
            ];
        }
        
        if (!empty($userAnswers)) {
            $testResult->userAnswers()->createMany($userAnswers);
        }

        // Clear session
        Session::forget(['accessed_test_code', 'participant_data', 'active_test_id']);
        Session::forget('test_answers_' . $test->id);
        Session::forget('test_start_time_' . $test->id);

        return redirect()->route('tests.result', $testResult);
    }

    /**
     * Menampilkan halaman hasil (LEGACY - untuk backward compatibility)
     */
    public function show(Test $test)
    {
        // Redirect ke sistem baru
        return redirect()->route('tests.question', ['test' => $test->id, 'number' => 1]);
    }

    /**
     * Store method (LEGACY - untuk backward compatibility)
     */
    public function store(Request $request, Test $test)
    {
        return $this->submitTest($test);
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