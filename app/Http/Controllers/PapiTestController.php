<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PapiQuestion;
use App\Models\PapiResult;
use Illuminate\Support\Facades\Session;

class PapiTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan form 90 soal PAPI.
     */
    public function showTestForm(Test $test)
    {
        // Ambil ID Alat Tes dari Modul Tes yang diterima
        $alatTesId = $test->alat_tes_id;

        // Query KRITIS: Ambil soal PAPI berdasarkan ID Alat Tes
        $questions = PapiQuestion::where('alat_tes_id', $alatTesId)
            ->orderBy('item_number')
            ->get();

        $remainingTime = $test->alatTes->duration_minutes * 60; // Dapatkan durasi

        if ($questions->isEmpty()) {
            return redirect()->back()->with('error', 'Soal PAPI Kostick belum diisi atau tidak terhubung.');
        }

        // Tampilkan View PAPI yang benar (dengan format pasangannya)
        return view('papi.test', compact('test', 'questions', 'remainingTime'));
    }

    /**
     * Memproses jawaban dan menghitung 20 skor (Logika Matriks).
     */
    public function submitTest(Request $request)
    {
        $user = auth()->user();

        $rules = [];
        for ($i = 1; $i <= 90; $i++) {
            $rules["item_{$i}"] = 'required|in:A,B';
        }
        $request->validate($rules);

        $scores = array_fill_keys(
            ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'],
            0
        );
        $rawAnswers = [];

        $itemsData = PapiQuestion::select('item_number', 'role_a', 'need_a', 'role_b', 'need_b')
            ->get()
            ->keyBy('item_number');

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

        $resultData = array_merge(
            [
                'user_id' => $user->id,
                'completed_at' => now(),
                'answers' => $rawAnswers,
                'participant_name' => $user->name ?? ($participantData['participant_name'] ?? null),
                'test_date' => now()->format('Y-m-d'),
                'interpretation' => 'Hasil sedang diproses...',
            ],
            $scores
        );

        PapiResult::create($resultData);

        return redirect()->route('papi.result.show', $user->id)
            ->with('success', 'Tes PAPI Kostick selesai. Skor telah dihitung.');
    }
}
