<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserTestController extends Controller
{
    /**
     * Menampilkan halaman pengerjaan tes.
     */
    public function show(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes terlebih dahulu.');
        }
        
        $test->load('questions.options');
        return view('test', compact('test'));
    }

    /**
     * Menyimpan hasil tes.
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
     * Menampilkan halaman hasil.
     * VERSI BARU: Hanya menampilkan pesan selesai, tanpa skor/detail.
     */
    public function result(TestResult $testResult)
    {
        // Kita hanya butuh data tes untuk menampilkan judulnya.
        $testResult->load('test');
        
        // Tidak perlu lagi mencari interpretasi atau memuat jawaban.
        // Cukup tampilkan view sederhana.
        return view('results', compact('testResult'));
    }
}
