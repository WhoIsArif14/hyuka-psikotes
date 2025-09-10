<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\InterpretationRule; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserTestController extends Controller
{
    /**
     * Menampilkan halaman pengerjaan tes.
     */
    public function show(Test $test)
    {
        // Validasi: pastikan pengguna datang dari alur yang benar
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
        // Pastikan pengguna datang dari alur yang benar
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $request->validate(['questions' => ['required', 'array']]);

        // Ambil data diri peserta dari session
        $participantData = Session::get('participant_data', []);
        
        $score = 0;
        $selectedOptionIds = $request->input('questions');
        $options = Option::findMany(array_values($selectedOptionIds));
        foreach ($options as $option) {
            $score += $option->point;
        }

        // Simpan semua data diri ke database
        $testResult = TestResult::create([
            'test_id' => $test->id,
            'score' => $score,
            'start_time' => now(), // Placeholder
            'end_time' => now(),
            // Menggabungkan data dari session
            'participant_name' => $participantData['participant_name'] ?? null,
            'participant_email' => $participantData['participant_email'] ?? null,
            'phone_number' => $participantData['phone_number'] ?? null,
            'education' => $participantData['education'] ?? null,
            'major' => $participantData['major'] ?? null,
        ]);

        // Simpan detail jawaban
        $userAnswers = [];
        foreach ($selectedOptionIds as $question_id => $option_id) {
            $userAnswers[] = [
                'test_result_id' => $testResult->id,
                'question_id' => $question_id,
                'option_id' => $option_id,
            ];
        }
        $testResult->userAnswers()->createMany($userAnswers);

        // Hapus data dari session setelah selesai
        Session::forget(['accessed_test_code', 'participant_data', 'active_test_id']);

        return redirect()->route('tests.result', $testResult);
    }

    /**
     * Menampilkan halaman hasil.
     */
    public function result(TestResult $testResult)
    {
        $testResult->load('test.questions.options', 'userAnswers.option');

        // --- PERBAIKAN UTAMA ADA DI SINI ---
        // Cari interpretasi yang cocok dengan skor peserta
        $interpretation = InterpretationRule::where('test_id', $testResult->test_id)
                            ->where('min_score', '<=', $testResult->score)
                            ->where('max_score', '>=', $testResult->score)
                            ->first();
        
        // Kirim variabel $interpretation ke view
        return view('results', compact('testResult', 'interpretation'));
    }
}

