<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\PapiResult;
use App\Models\Test;
use App\Models\AlatTes;
use Illuminate\Support\Facades\Session;

class PapiTestController extends Controller
{
    

    /**
     * Menampilkan form 90 soal PAPI.
     */
    public function showTestForm(Test $test, AlatTes $alatTes)
    {
        if (Session::get('active_test_id') != $test->id) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        // Query KRITIS: Ambil soal PAPI berdasarkan ID Alat Tes dari tabel questions
        $questions = Question::where('alat_tes_id', $alatTes->id)
            ->where('type', 'PAPIKOSTICK')
            ->orderBy('ranking_weight')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('tests.dashboard', $test->id)
                ->with('error', 'Soal PAPI Kostick belum diisi atau tidak terhubung dengan alat tes ini.');
        }

        // ✅ Mulai timer jika belum ada
        if (!Session::has('test_start_time_' . $alatTes->id)) {
            Session::put('test_start_time_' . $alatTes->id, now());
        }

        $startTime = Session::get('test_start_time_' . $alatTes->id);
        $timeLimit = $alatTes->duration_minutes * 60;
        $timeElapsed = now()->diffInSeconds($startTime);
        $timeRemaining = (int) max(0, $timeLimit - $timeElapsed);

        // ✅ Jika waktu habis, langsung submit
        if ($timeRemaining === 0) {
            return $this->submitTest(new Request(), $test, $alatTes);
        }

        // Tampilkan View PAPI yang benar (dengan format pasangannya)
        return view('papi.test', [
            'test' => $test,
            'alatTes' => $alatTes,
            'questions' => $questions,
            'timeRemaining' => $timeRemaining
        ]);
    }

    /**
     * Memproses jawaban dan menghitung 20 skor (Logika Matriks).
     */
    public function submitTest(Request $request, Test $test, AlatTes $alatTes)
    {
        $user = auth()->user();

        // Hitung jumlah item PAPI yang ada
        $totalItems = Question::where('alat_tes_id', $alatTes->id)
            ->where('type', 'PAPIKOSTICK')
            ->count();

        // Validasi jawaban
        $rules = [];
        for ($i = 1; $i <= $totalItems; $i++) {
            $rules["item_{$i}"] = 'required|in:A,B';
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika waktu habis dan ada jawaban kosong, isi dengan default
            $answers = $request->all();
            for ($i = 1; $i <= $totalItems; $i++) {
                if (!isset($answers["item_{$i}"])) {
                    $request->merge(["item_{$i}" => 'A']); // Default A jika kosong
                }
            }
        }

        // Inisialisasi skor untuk 20 dimensi PAPI
        $scores = array_fill_keys(
            ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'],
            0
        );
        $rawAnswers = [];

        // Ambil data soal PAPI dari tabel questions
        $itemsData = Question::where('alat_tes_id', $alatTes->id)
            ->where('type', 'PAPIKOSTICK')
            ->get()
            ->keyBy(function($q) {
                return $q->ranking_weight; // item_number
            });

        // Hitung skor berdasarkan jawaban
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'item_')) {
                $item_number = (int)str_replace('item_', '', $key);
                $item = $itemsData->get($item_number);

                if ($item) {
                    $choice = $value;
                    $rawAnswers[$item_number] = $choice;

                    if ($choice === 'A') {
                        $scores[$item->role_a]++;
                        $scores[$item->need_a]++;
                    } elseif ($choice === 'B') {
                        $scores[$item->role_b]++;
                        $scores[$item->need_b]++;
                    }
                }
            }
        }

        $participantData = Session::get('participant_data', []);
        $startTime = Session::get('test_start_time_' . $alatTes->id, now());

        // Simpan hasil
        $resultData = array_merge(
            [
                'user_id' => $user->id,
                'test_id' => $test->id,
                'alat_tes_id' => $alatTes->id,
                'completed_at' => now(),
                'start_time' => $startTime,
                'end_time' => now(),
                'answers' => json_encode($rawAnswers),
                'participant_name' => $user->name ?? ($participantData['participant_name'] ?? null),
                'participant_email' => $participantData['participant_email'] ?? null,
                'phone_number' => $participantData['phone_number'] ?? null,
                'education' => $participantData['education'] ?? null,
                'major' => $participantData['major'] ?? null,
                'test_date' => now()->format('Y-m-d'),
                'interpretation' => 'Hasil sedang diproses...',
            ],
            $scores
        );

        PapiResult::create($resultData);

        // Hapus session
        Session::forget('test_start_time_' . $alatTes->id);

        // ✅ REDIRECT KE DASHBOARD MODUL
        return redirect()->route('tests.dashboard', $test->id)
            ->with('success', 'Tes PAPI Kostick berhasil diselesaikan!');
    }
}