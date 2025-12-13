<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\RmibQuestion;
use App\Models\RmibAnswer;
use App\Models\RmibResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RmibTestController extends Controller
{
    /**
     * Tampilkan form tes RMIB
     */
    public function showTestForm(Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $test->load('alatTes');
        
        // Cari Alat Tes RMIB
        $rmibAlatTes = $test->alatTes->firstWhere(function ($alat) {
            $slug = strtolower($alat->slug ?? $alat->name ?? '');
            return str_contains($slug, 'rmib');
        });

        if (!$rmibAlatTes) {
            return redirect()->route('login')
                ->with('error', 'Alat Tes RMIB tidak ditemukan.');
        }

        // Ambil semua pertanyaan RMIB untuk alat tes ini
        $questions = RmibQuestion::where('alat_tes_id', $rmibAlatTes->id)
            ->orderBy('item_number')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('login')
                ->with('error', 'Belum ada soal RMIB. Hubungi administrator.');
        }

        // Reset session jika mulai baru
        Session::forget('rmib_answers_' . $test->id);
        Session::forget('rmib_start_time_' . $test->id);
        Session::put('rmib_start_time_' . $test->id, now());

        // Redirect ke tabel pertama
        return redirect()->route('tests.rmib.table', [
            'test' => $test->id,
            'table' => 1
        ]);
    }

    /**
     * Tampilkan tabel RMIB berdasarkan nomor
     */
    public function showTable(Test $test, $tableNumber)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $test->load('alatTes');
        
        $rmibAlatTes = $test->alatTes->firstWhere(function ($alat) {
            $slug = strtolower($alat->slug ?? $alat->name ?? '');
            return str_contains($slug, 'rmib');
        });

        if (!$rmibAlatTes) {
            return redirect()->route('login')->with('error', 'Alat Tes RMIB tidak ditemukan.');
        }

        // Ambil pertanyaan untuk tabel ini
        $questions = RmibQuestion::where('alat_tes_id', $rmibAlatTes->id)
            ->orderBy('item_number')
            ->get();

        $totalTables = $questions->count();

        if ($tableNumber < 1 || $tableNumber > $totalTables) {
            return redirect()->route('tests.rmib.table', [
                'test' => $test->id,
                'table' => 1
            ]);
        }

        $currentQuestion = $questions[$tableNumber - 1];
        
        // Ambil jawaban yang sudah disimpan
        $savedAnswers = Session::get('rmib_answers_' . $test->id, []);
        $savedRanks = $savedAnswers[$currentQuestion->id] ?? [];

        // Cek waktu tersisa
        $startTime = Session::get('rmib_start_time_' . $test->id);
        $timeElapsed = now()->diffInSeconds($startTime);
        $timeLimit = $test->duration_minutes * 60;
        $timeRemaining = (int) max(0, $timeLimit - $timeElapsed);

        return view('tests.test-rmib', [
            'test' => $test,
            'alatTes' => $rmibAlatTes,
            'question' => $currentQuestion,
            'currentTable' => $tableNumber,
            'totalTables' => $totalTables,
            'savedRanks' => $savedRanks,
            'timeRemaining' => $timeRemaining,
            'statements' => $currentQuestion->getStatementsArray()
        ]);
    }

    /**
     * Simpan jawaban tabel RMIB
     */
    public function saveTable(Request $request, Test $test, $tableNumber)
    {
        if (Session::get('active_test_id') != $test->id) {
            return response()->json(['error' => 'Sesi tidak valid'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:rmib_questions,id',
            'ranks' => 'required|array',
            'ranks.*' => 'required|integer|min:1|max:12'
        ]);

        // Validasi: pastikan tidak ada ranking yang sama
        $ranks = $request->input('ranks');
        if (count($ranks) !== count(array_unique($ranks))) {
            return response()->json([
                'error' => 'Setiap profesi harus memiliki ranking yang berbeda!'
            ], 422);
        }

        // Validasi: pastikan semua ranking dari 1-12 ada
        $expectedRanks = range(1, 12);
        if (array_diff($expectedRanks, array_values($ranks))) {
            return response()->json([
                'error' => 'Anda harus memberikan ranking 1 sampai 12 untuk semua profesi!'
            ], 422);
        }

        // Simpan ke session
        $answers = Session::get('rmib_answers_' . $test->id, []);
        $answers[$request->question_id] = $ranks;
        Session::put('rmib_answers_' . $test->id, $answers);

        return response()->json(['success' => true]);
    }

    /**
     * Submit seluruh tes RMIB
     */
    public function submitTest(Request $request, Test $test)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        DB::beginTransaction();

        try {
            $test->load('alatTes');
            
            $rmibAlatTes = $test->alatTes->firstWhere(function ($alat) {
                $slug = strtolower($alat->slug ?? $alat->name ?? '');
                return str_contains($slug, 'rmib');
            });

            $questions = RmibQuestion::where('alat_tes_id', $rmibAlatTes->id)
                ->orderBy('item_number')
                ->get();

            $savedAnswers = Session::get('rmib_answers_' . $test->id, []);

            // Validasi: pastikan semua tabel sudah dijawab
            if (count($savedAnswers) !== $questions->count()) {
                return back()->with('error', 'Anda belum menyelesaikan semua tabel!');
            }

            // Simpan jawaban ke database
            foreach ($questions as $question) {
                $ranks = $savedAnswers[$question->id] ?? [];
                
                RmibAnswer::create([
                    'user_id' => auth()->id(),
                    'alat_tes_id' => $rmibAlatTes->id,
                    'rmib_question_id' => $question->id,
                    'rank_a' => $ranks['A'] ?? null,
                    'rank_b' => $ranks['B'] ?? null,
                    'rank_c' => $ranks['C'] ?? null,
                    'rank_d' => $ranks['D'] ?? null,
                    'rank_e' => $ranks['E'] ?? null,
                    'rank_f' => $ranks['F'] ?? null,
                    'rank_g' => $ranks['G'] ?? null,
                    'rank_h' => $ranks['H'] ?? null,
                    'rank_i' => $ranks['I'] ?? null,
                    'rank_j' => $ranks['J'] ?? null,
                    'rank_k' => $ranks['K'] ?? null,
                    'rank_l' => $ranks['L'] ?? null,
                ]);
            }

            // Hitung hasil
            $result = $this->calculateRmibResult($rmibAlatTes->id, $savedAnswers, $questions);

            DB::commit();

            // Clear session
            Session::forget('rmib_answers_' . $test->id);
            Session::forget('rmib_start_time_' . $test->id);

            // ✅ REDIRECT KE DASHBOARD MODUL (jangan hapus active_test_id, user masih perlu akses modul)
            return redirect()->route('tests.dashboard', $test->id)
                ->with('success', 'Tes RMIB berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Submit Error: ' . $e->getMessage());
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan hasil: ' . $e->getMessage());
        }
    }

    /**
     * Hitung hasil RMIB
     */
    private function calculateRmibResult($alatTesId, $savedAnswers, $questions)
    {
        $scores = [
            'outdoor' => 0, 'mechanical' => 0, 'computational' => 0, 'scientific' => 0,
            'personal' => 0, 'aesthetic' => 0, 'literary' => 0, 'musical' => 0,
            'social' => 0, 'clerical' => 0, 'practical' => 0, 'medical' => 0
        ];

        foreach ($questions as $question) {
            $ranks = $savedAnswers[$question->id] ?? [];
            $keys = $question->getKeysArray();

            foreach ($keys as $letter => $interestCode) {
                if (isset($ranks[$letter]) && $interestCode) {
                    // Scoring: ranking 1 = 12 poin, ranking 12 = 1 poin
                    $points = 13 - $ranks[$letter];
                    
                    // Map interest code ke field name
                    $fieldMap = [
                        'O' => 'outdoor', 'M' => 'mechanical', 'C' => 'computational',
                        'S' => 'scientific', 'P' => 'personal', 'A' => 'aesthetic',
                        'L' => 'literary', 'Mu' => 'musical', 'SS' => 'social',
                        'Cl' => 'clerical', 'Pr' => 'practical', 'Me' => 'medical'
                    ];

                    if (isset($fieldMap[$interestCode])) {
                        $scores[$fieldMap[$interestCode]] += $points;
                    }
                }
            }
        }

        // Urutkan scores untuk mendapatkan top 3
        arsort($scores);
        $topThree = array_slice(array_keys($scores), 0, 3);

        // Simpan hasil
        return RmibResult::create([
            'user_id' => auth()->id(),
            'alat_tes_id' => $alatTesId,
            'score_outdoor' => $scores['outdoor'],
            'score_mechanical' => $scores['mechanical'],
            'score_computational' => $scores['computational'],
            'score_scientific' => $scores['scientific'],
            'score_personal' => $scores['personal'],
            'score_aesthetic' => $scores['aesthetic'],
            'score_literary' => $scores['literary'],
            'score_musical' => $scores['musical'],
            'score_social' => $scores['social'],
            'score_clerical' => $scores['clerical'],
            'score_practical' => $scores['practical'],
            'score_medical' => $scores['medical'],
            'interest_ranking' => $scores,
            'top_interest_1' => $topThree[0] ?? null,
            'top_interest_2' => $topThree[1] ?? null,
            'top_interest_3' => $topThree[2] ?? null,
            'completed_at' => now(),
        ]);
    }

    /**
     * Tampilkan hasil RMIB (HANYA UNTUK ADMIN)
     */
    public function showResult(RmibResult $result)
    {
        // Cek apakah user adalah admin
        if (!auth()->user()->is_admin && auth()->id() !== $result->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat hasil ini.');
        }
        
        $result->load('user', 'alatTes');
        
        return view('questions.show_rmib_result', compact('result'));
    }
}