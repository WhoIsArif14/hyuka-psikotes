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
     * Akan digunakan ketika route dipanggil tanpa memberikan {test}.
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

        return $this->show($test);
    }

    /**
     * Menampilkan halaman pengerjaan tes umum (non-PAPI).
     */
    public function show(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }

        \Log::info('Test ID: ' . $test->id);

        // Muat relasi alatTes & pertanyaannya
        $test->load('alatTes.questions');

        // 🔧 Ambil alat tes pertama yang memiliki soal
        $alatTes = $test->alatTes->firstWhere(fn($alat) => $alat->questions->isNotEmpty());

        if (!$alatTes) {
            \Log::error('Tidak ada questions untuk test_id: ' . $test->id);
            return redirect()->route('login')
                ->with('error', 'Belum ada soal untuk tes ini. Hubungi administrator.');
        }

        \Log::info('Menampilkan alat tes: ' . $alatTes->name . ' | Jumlah Soal: ' . $alatTes->questions->count());

        return view('test', [
            'test' => $test,
            'alatTes' => $alatTes
        ]);
    }

    /**
     * Menyimpan hasil tes umum (non-PAPI).
     */
    public function store(Request $request, Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $request->validate(['questions' => ['required', 'array']]);
        $participantData = Session::get('participant_data', []);

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
