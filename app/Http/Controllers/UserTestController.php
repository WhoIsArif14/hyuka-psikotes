<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Option;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserTestController extends Controller
{
    // ... method show() dan store() yang sudah ada ...

    /**
     * Menampilkan halaman pengerjaan tes untuk tes yang dipilih.
     */
    public function show(Test $test)
    {
        if (!$test->is_published) {
            abort(404);
        }
        $test->load('questions.options');
        return view('test', compact('test'));
    }

    /**
     * Menyimpan jawaban pengguna dan menghitung skor.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate(['questions' => ['required', 'array']]);
        $score = 0;
        $userAnswers = [];
        $selectedOptionIds = $request->input('questions');
        $options = Option::findMany(array_values($selectedOptionIds));
        foreach ($options as $option) {
            $score += $option->point;
        }

        DB::beginTransaction();
        try {
            $testResult = TestResult::create([
                'test_id' => $test->id,
                'user_id' => Auth::id(),
                'score' => 0,
                'start_time' => now(),
            ]);
            foreach ($selectedOptionIds as $question_id => $option_id) {
                $userAnswers[] = [
                    'test_result_id' => $testResult->id,
                    'question_id' => $question_id,
                    'option_id' => $option_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $testResult->userAnswers()->insert($userAnswers);
            $testResult->update(['score' => $score, 'end_time' => now()]);
            DB::commit();
            return redirect()->route('tests.result', $testResult);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan hasil tes. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan halaman hasil tes.
     */
    public function result(TestResult $testResult)
    {
        // Pastikan hanya user yang bersangkutan yang bisa melihat hasilnya
        if ($testResult->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Ambil relasi test untuk menampilkan judul tes
        $testResult->load('test');

        return view('results', compact('testResult'));
    }
}