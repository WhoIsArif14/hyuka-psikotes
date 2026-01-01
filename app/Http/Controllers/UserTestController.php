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

            // If this is the moment the user starts this alat tes for the first time in this session
            // clear any stale saved answers so the UI shows no pre-selected options.
            if (!Session::has('test_start_time_' . $alatTes->id)) {
                Session::forget('test_answers_' . $alatTes->id);
                Session::forget('answered_questions_' . $alatTes->id);
                Session::put('test_start_time_' . $alatTes->id, now());
            }

            // Update atau buat entry progress untuk user saat memulai alat tes ini
            if (auth()->check()) {
                \App\Models\ProgressPengerjaan::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'test_id' => $test->id,
                        'status' => 'On Progress',
                    ],
                    [
                        'alat_tes_id' => $alatTes->id,
                        'current_module' => $alatTes->name ?? ($alatTes->slug ?? 'Alat Tes'),
                        'percentage' => 0,
                    ]
                );
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
        $answeredQuestions = Session::get('answered_questions_' . $alatTes->id, []);

        // Only treat a saved answer as valid if the question is marked in answered_questions (i.e., user deliberately saved it)
        if (!in_array($currentQuestion->id, $answeredQuestions, true)) {
            $savedAnswer = null;
        } else {
            $savedAnswer = $savedAnswers[$currentQuestion->id] ?? null;

            // Validate saved answer to avoid accidental pre-selection of invalid/default values
            // For multiple-choice questions ensure the saved answer maps to an existing option index
            if ($currentQuestion->type !== 'PAPIKOSTICK') {
                $options = is_string($currentQuestion->options)
                    ? json_decode($currentQuestion->options, true)
                    : $currentQuestion->options ?? [];

                if (is_array($options) && count($options) > 0) {
                    if (is_array($savedAnswer)) {
                        $savedAnswer = array_values(array_filter($savedAnswer, function ($a) use ($options) {
                            return isset($options[$a]);
                        }));
                        if (empty($savedAnswer)) {
                            $savedAnswer = null;
                        }
                    } else {
                        if (!isset($options[$savedAnswer])) {
                            $savedAnswer = null;
                        }
                    }
                } else {
                    // No options defined, clear any saved answer
                    $savedAnswer = null;
                }
            } else {
                // For PAPIKOSTICK ensure only 'A' or 'B' are accepted
                if (!in_array($savedAnswer, ['A', 'B'], true)) {
                    $savedAnswer = null;
                }
            }
        }

        // If session contained an invalid/unsupported saved answer, remove it to avoid showing
        // answered state in the navigator for an actually unanswered question.
        if (isset($savedAnswers[$currentQuestion->id]) && $savedAnswer === null) {
            unset($savedAnswers[$currentQuestion->id]);
            Session::put('test_answers_' . $alatTes->id, $savedAnswers);

            // Also remove from answered_questions list
            if (in_array($currentQuestion->id, $answeredQuestions, true)) {
                $answeredQuestions = array_values(array_diff($answeredQuestions, [$currentQuestion->id]));
                Session::put('answered_questions_' . $alatTes->id, $answeredQuestions);
            }
        }

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
        $answered = Session::get('answered_questions_' . $alatTes->id, []);

        if ($request->has('answer')) {
            $answers[$currentQuestion->id] = $request->input('answer');

            // Mark this question as answered (so we know it's a deliberate saved response)
            if (!in_array($currentQuestion->id, $answered)) {
                $answered[] = $currentQuestion->id;
            }
        } else {
            // If the request didn't include an answer (e.g., navigating), do not mark as answered.
            // Optionally allow unsetting answer when user clears; for now we keep previous value.
        }

        Session::put('test_answers_' . $alatTes->id, $answers);
        Session::put('answered_questions_' . $alatTes->id, $answered);

        // Update progress percentage (jika user terautentikasi)
        $answeredCount = count($answered);
        $totalQuestions = $questions->count();
        $progressPercent = $totalQuestions > 0 ? (int) round(($answeredCount / $totalQuestions) * 100) : 0;

        if (auth()->check()) {
            \App\Models\ProgressPengerjaan::where('user_id', auth()->id())
                ->where('test_id', $test->id)
                ->where('status', 'On Progress')
                ->update([
                    'percentage' => $progressPercent,
                    'current_module' => $alatTes->name ?? ($alatTes->slug ?? 'Alat Tes'),
                ]);
        }

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
            if (!isset($savedAnswers[$question->id])) {
                continue;
            }

            $userAnswer = $savedAnswers[$question->id];

            $options = is_string($question->options)
                ? json_decode($question->options, true)
                : $question->options ?? [];

            if (is_array($userAnswer)) {
                foreach ($userAnswer as $optIndex) {
                    if (isset($options[$optIndex])) {
                        $point = $options[$optIndex]['point'] ?? 0;
                        $score += $point;
                        $selectedOptionIds[$question->id][] = $optIndex;
                    }
                }
            } else {
                $optIndex = $userAnswer;
                if (isset($options[$optIndex])) {
                    $point = $options[$optIndex]['point'] ?? 0;
                    $score += $point;
                    $selectedOptionIds[$question->id] = $optIndex;
                }
            }
        }

        $startTime = Session::get('test_start_time_' . $alatTes->id, now());

        // Hitung skor maksimum dari semua soal untuk normalisasi IQ
        $maxScore = 0;
        foreach ($questions as $q) {
            $opts = is_string($q->options) ? json_decode($q->options, true) : $q->options ?? [];
            $maxForQ = 0;
            if (is_array($opts)) {
                foreach ($opts as $opt) {
                    $p = $opt['point'] ?? 0;
                    if ($p > $maxForQ) $maxForQ = $p;
                }
            } else {
                $maxForQ = 1;
            }
            $maxScore += $maxForQ;
        }

        $iq = \App\Models\TestResult::computeIq($score, $maxScore);

        $testResult = TestResult::create([
            'test_id' => $test->id,
            'alat_tes_id' => $alatTes->id,
            'user_id' => auth()->id(),
            'score' => $score,
            'iq' => $iq,
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

        // Tandai progress sebagai selesai jika ada
        if (auth()->check()) {
            \App\Models\ProgressPengerjaan::where('user_id', auth()->id())
                ->where('test_id', $test->id)
                ->update([
                    'status' => 'Completed',
                    'percentage' => 100,
                    'current_module' => 'Selesai',
                    'alat_tes_id' => $alatTes->id,
                ]);
        }

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
