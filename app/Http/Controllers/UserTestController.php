<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestResult;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserTestController extends Controller
{
    /**
     * Menampilkan halaman pengerjaan tes.
     */
    public function show(Test $test)
    {
        // Keamanan: Pastikan peserta sudah melewati langkah pengisian nama.
        if (!Session::has('participant_name') || Session::get('active_test_id') != $test->id) {
            // Jika belum, kembalikan ke halaman awal.
            return redirect()->route('login')->with('error', 'Silakan masukkan kode tes dan nama Anda terlebih dahulu.');
        }

        $test->load('questions.options');
        return view('test', compact('test'));
    }

    /**
     * Menyimpan hasil tes dari peserta tanpa akun.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'questions' => ['required', 'array']
        ]);

        // Ambil nama peserta dari session
        $participantName = Session::get('participant_name');
        if (!$participantName) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir, silakan mulai lagi.');
        }

        $score = 0;
        $userAnswers = [];
        $selectedOptionIds = $request->input('questions');

        $options = Option::findMany(array_values($selectedOptionIds));
        foreach ($options as $option) {
            $score += $option->point;
        }

        DB::beginTransaction();
        try {
            // Buat record hasil tes, simpan nama peserta
            $testResult = TestResult::create([
                'test_id' => $test->id,
                'participant_name' => $participantName, // <-- Simpan nama di sini
                'user_id' => null, // <-- Kosongkan user_id
                'score' => $score,
                'start_time' => now(), // Placeholder
                'end_time' => now(),
            ]);

            // Siapkan data untuk tabel user_answers
            foreach ($selectedOptionIds as $question_id => $option_id) {
                $userAnswers[] = [
                    'test_result_id' => $testResult->id,
                    'question_id' => $question_id,
                    'option_id' => $option_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Simpan semua jawaban
            $testResult->userAnswers()->insert($userAnswers);

            DB::commit();

            // Hapus session setelah tes selesai
            Session::forget(['participant_name', 'active_test_id']);

            return redirect()->route('tests.result', $testResult);

        } catch (\Exception $e) {
            DB::rollBack();
            // Tampilkan error untuk debugging, bisa diubah nanti
            return redirect()->route('login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman hasil.
     */
    public function result(TestResult $testResult)
    {
        $testResult->load('test');
        // Logika untuk interpretasi bisa ditambahkan di sini jika diperlukan
        return view('results', compact('testResult'));
    }
}

