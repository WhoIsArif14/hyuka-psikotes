<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule;
use App\Models\PapiResult;
use App\Models\RmibResult;
use App\Models\AlatTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class UserTestController extends Controller
{
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

    public function startTest(Test $test)
    {

        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan masukkan kode tes terlebih dahulu.');
        }

        // ✅ REDIRECT KE DASHBOARD MODUL
        return redirect()->route('tests.dashboard', $test->id);
    }

    // ✅ DASHBOARD MODUL - Menampilkan semua alat tes dalam modul
    public function showDashboard(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        $test->load('alatTes');

        // Get ordered alat tes
        $orderedAlatTesIds = is_string($test->test_order)
            ? json_decode($test->test_order, true)
            : ($test->test_order ?? $test->alatTes->pluck('id')->toArray());

        // Get alat tes in correct order
        $alatTesCollection = $test->alatTes->keyBy('id');
        $alatTesList = collect($orderedAlatTesIds)
            ->map(fn($id) => $alatTesCollection->get($id))
            ->filter();

        $userId = auth()->id();
        $completedAlatTesIds = [];

        // Check which alat tes are completed
        foreach ($alatTesList as $alatTes) {
            $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
            $isFinished = false;

            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                $isFinished = PapiResult::where('user_id', $userId)->exists();
            } elseif (str_contains($slug, 'rmib')) {
                $isFinished = RmibResult::where('user_id', $userId)
                    ->where('alat_tes_id', $alatTes->id)->exists();
            } else {
                $isFinished = TestResult::where('user_id', $userId)
                    ->where('alat_tes_id', $alatTes->id)
                    ->exists();
            }

            if ($isFinished) {
                $completedAlatTesIds[] = $alatTes->id;
            }
        }

        $totalAlatTes = $alatTesList->count();
        $completedCount = count($completedAlatTesIds);
        $progressPercentage = $totalAlatTes > 0 ? round(($completedCount / $totalAlatTes) * 100) : 0;

        // Check if all completed
        if ($completedCount === $totalAlatTes && $totalAlatTes > 0) {
            Session::forget(['accessed_test_code', 'participant_data', 'active_test_id']);
            return redirect()->route('tests.module.finish', $test->id)
                ->with('success', 'Selamat, Anda telah menyelesaikan semua alat tes dalam modul ini!');
        }

        return view('module-dashboard', compact(
            'test',
            'alatTesList',
            'completedAlatTesIds',
            'totalAlatTes',
            'completedCount',
            'progressPercentage'
        ));
    }

    // ✅ HALAMAN 1: PERSIAPKAN DIRI
    public function showPreparation(Test $test, AlatTes $alatTes)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        // Load example questions dari database
        $exampleQuestions = is_string($alatTes->example_questions)
            ? json_decode($alatTes->example_questions, true)
            : ($alatTes->example_questions ?? []);

        return view('test-preparation', [
            'test' => $test,
            'alatTes' => $alatTes,
            'exampleQuestions' => $exampleQuestions
        ]);
    }

    // ✅ HALAMAN 2: PETUNJUK SOAL
    public function showInstructions(Test $test, AlatTes $alatTes)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        // Load example questions
        $exampleQuestions = is_string($alatTes->example_questions)
            ? json_decode($alatTes->example_questions, true)
            : ($alatTes->example_questions ?? []);

        return view('test-instructions', [
            'test' => $test,
            'alatTes' => $alatTes,
            'exampleQuestions' => $exampleQuestions
        ]);
    }

    // ✅ PROSES MULAI TES SETELAH PETUNJUK
    public function startAlatTes(Test $test, AlatTes $alatTes)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');

        // Redirect ke controller yang sesuai berdasarkan jenis alat tes
        if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
            $papiController = new \App\Http\Controllers\PapiTestController();
            return $papiController->showTestForm($test, $alatTes);
        }

        if (str_contains($slug, 'rmib')) {
            $rmibController = new \App\Http\Controllers\RmibTestController();
            return $rmibController->showTestForm($test, $alatTes);
        }

        // Alat Tes Umum - Load questions terlebih dahulu
        $alatTes->load('questions');

        if ($alatTes->questions->isNotEmpty()) {
            Session::forget('current_alat_tes_id');
            Session::put('current_alat_tes_id', $alatTes->id);

            if (!Session::has('test_start_time_' . $alatTes->id)) {
                Session::put('test_start_time_' . $alatTes->id, now());
            }

            return redirect()->route('tests.question', [
                'test' => $test->id,
                'alat_tes' => $alatTes->id,
                'number' => 1
            ]);
        }

        return redirect()->route('tests.dashboard', $test->id)
            ->with('error', 'Alat tes "' . $alatTes->name . '" belum memiliki soal. Silakan pilih alat tes lain atau hubungi administrator.');
    }

    protected function getNextAlatTes(Test $test, array $orderedAlatTesIds, int $userId)
    {
        $alatTesCollection = $test->alatTes->keyBy('id');

        foreach ($orderedAlatTesIds as $alatTesId) {
            $alatTes = $alatTesCollection->get($alatTesId);

            if (!$alatTes) continue;

            $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
            $isFinished = false;

            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                $isFinished = PapiResult::where('user_id', $userId)->exists();
            } elseif (str_contains($slug, 'rmib')) {
                $isFinished = RmibResult::where('user_id', $userId)
                    ->where('alat_tes_id', $alatTesId)->exists();
            } else {
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

    public function showQuestion(Test $test, AlatTes $alatTes, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        if ($test->AlatTes()->where('alat_tes.id', $alatTes->id)->doesntExist()) {
            return redirect()->route('tests.start')->with('error', 'Alat tes ini bukan bagian dari modul yang aktif.');
        }

        $alatTes->load('questions');
        $questions = $alatTes->questions;
        $totalQuestions = $questions->count();

        if ($number < 1 || $number > $totalQuestions) {
            return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => 1]);
        }

        $currentQuestion = $questions[$number - 1];
        $savedAnswers = Session::get('test_answers_' . $alatTes->id, []);
        $savedAnswer = $savedAnswers[$currentQuestion->id] ?? null;

        $startTime = Session::get('test_start_time_' . $alatTes->id);
        $timeLimit = $alatTes->duration_minutes * 60;
        $timeElapsed = now()->diffInSeconds($startTime);
        $timeRemaining = (int) max(0, $timeLimit - $timeElapsed);

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

    public function saveAnswer(Request $request, Test $test, AlatTes $alatTes, $number)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $alatTes->load('questions');
        $questions = $alatTes->questions;
        $currentQuestion = $questions[$number - 1];

        $answers = Session::get('test_answers_' . $alatTes->id, []);

        if ($request->has('answer')) {
            $answers[$currentQuestion->id] = $request->input('answer');
        }

        Session::put('test_answers_' . $alatTes->id, $answers);

        $action = $request->input('action');

        // Handle navigasi ke nomor soal tertentu
        if ($request->has('navigate_to')) {
            $navigateTo = $request->input('navigate_to');
            if ($navigateTo >= 1 && $navigateTo <= $questions->count()) {
                return redirect()->route('tests.question', ['test' => $test->id, 'alat_tes' => $alatTes->id, 'number' => $navigateTo]);
            }
        }

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

        $startTime = Session::get('test_start_time_' . $alatTes->id, now());

        $testResult = TestResult::create([
            'test_id' => $test->id,
            'alat_tes_id' => $alatTes->id,
            'user_id' => auth()->id(),
            'score' => $score,
            'start_time' => $startTime,
            'end_time' => now(),
            'participant_name' => $participantData['participant_name'] ?? null,
            'participant_email' => $participantData['participant_email'] ?? null,
            'phone_number' => $participantData['phone_number'] ?? null,
            'education' => $participantData['education'] ?? null,
            'major' => $participantData['major'] ?? null,
        ]);

        Session::forget('test_answers_' . $alatTes->id);
        Session::forget('test_start_time_' . $alatTes->id);
        Session::forget('current_alat_tes_id');

        // ✅ REDIRECT KE DASHBOARD MODUL
        return redirect()->route('tests.dashboard', $test->id)
            ->with('success', 'Tes ' . $alatTes->name . ' berhasil diselesaikan!');
    }

    public function finishModule(Test $test)
    {
        return view('test-completion', compact('test'));
    }

    public function show(Test $test)
    {
        return redirect()->route('tests.start');
    }

    public function store(Request $request, Test $test)
    {
        return redirect()->route('tests.start')->with('error', 'Submission gagal, gunakan alur Alat Tes.');
    }

    public function result(TestResult $testResult)
    {
        $testResult->load('test');
        return view('results', compact('testResult'));
    }
}
