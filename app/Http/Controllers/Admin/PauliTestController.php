<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PauliTest;
use App\Models\AlatTes;
use App\Models\Question;
use App\Jobs\GeneratePauliQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PauliTestController extends Controller
{
    /**
     * Tampilkan daftar Pauli Test untuk Alat Tes tertentu
     * Route: /admin/alat-tes/{alatTesId}/pauli
     */
    public function index($alatTesId)
    {
        $alatTes = AlatTes::findOrFail($alatTesId);

        $pauliTests = PauliTest::where('alat_tes_id', $alatTesId)
            ->with('results')
            ->withCount('generatedQuestions')
            ->paginate(10);

        return view('admin.questions.index_pauli', compact('alatTes', 'pauliTests'));
    }

    /**
     * Form create Pauli Test
     */
    public function create(Request $request)
    {
        $alatTesId = $request->get('alat_tes_id');

        if (!$alatTesId) {
            return redirect()->back()->with('error', 'Alat Tes ID tidak ditemukan');
        }

        $alatTes = AlatTes::findOrFail($alatTesId);

        return view('admin.questions.create_pauli', compact('alatTes'));
    }

    /**
     * Store Pauli Test
     */
    public function store(Request $request)
    {
        // If auto-generate flag is present, only validate alat_tes_id and create default config
        if ($request->has('auto_generate_pauli') && $request->auto_generate_pauli == '1') {
            $validated = $request->validate([
                'alat_tes_id' => 'required|exists:alat_tes,id',
            ]);

            // Check if a configuration already exists
            $existing = PauliTest::where('alat_tes_id', $validated['alat_tes_id'])->first();
            if ($existing) {
                return redirect()
                    ->route('admin.pauli.index', $validated['alat_tes_id'])
                    ->with('info', 'Konfigurasi Pauli Test sudah ada untuk Alat Tes ini.');
            }
            DB::beginTransaction();
            try {
                $pauliTest = PauliTest::create([
                    'alat_tes_id' => $validated['alat_tes_id'],
                    'total_columns' => 45,
                    'pairs_per_column' => 45,
                    'time_per_column' => 60,
                ]);

                DB::commit();

                $persistResult = $this->persistPauliQuestions($pauliTest);

                $message = 'Konfigurasi Pauli Test default berhasil di-generate!';
                if (is_array($persistResult) && isset($persistResult['queued']) && $persistResult['queued']) {
                    $message .= ' Soal akan dibuat di background (proses queue).';
                } elseif (is_array($persistResult) && isset($persistResult['created'])) {
                    $message .= " Menyimpan {$persistResult['created']} soal PAULI ke database.";
                }

                return redirect()
                    ->route('admin.pauli.index', $validated['alat_tes_id'])
                    ->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create PauliTest and persist questions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

                return back()->with('error', 'Gagal membuat konfigurasi Pauli: ' . $e->getMessage());
            }
        }

        $validated = $request->validate([
            'alat_tes_id' => 'required|exists:alat_tes,id',
            'total_columns' => 'required|integer|min:1|max:60',
            'pairs_per_column' => 'required|integer|min:1|max:60',
            'time_per_column' => 'required|integer|min:10|max:300',
        ]);

        PauliTest::create($validated);

        return redirect()
            ->route('admin.pauli.index', $validated['alat_tes_id'])
            ->with('success', 'Konfigurasi Pauli Test berhasil dibuat!');
    }

    /**
     * Form edit Pauli Test
     */
    public function edit(PauliTest $pauliTest)
    {
        $alatTes = $pauliTest->alatTes;
        return view('admin.questions.edit_pauli', compact('pauliTest', 'alatTes'));
    }

    /**
     * Update Pauli Test
     */
    public function update(Request $request, PauliTest $pauliTest)
    {
        $validated = $request->validate([
            'alat_tes_id' => 'required|exists:alat_tes,id',
            'total_columns' => 'required|integer|min:1|max:60',
            'pairs_per_column' => 'required|integer|min:1|max:60',
            'time_per_column' => 'required|integer|min:10|max:300',
        ]);

        $pauliTest->update($validated);

        return redirect()
            ->route('admin.pauli.index', $validated['alat_tes_id'])
            ->with('success', 'Konfigurasi Pauli Test berhasil diupdate!');
    }

    /**
     * Hapus Pauli Test
     */
    public function destroy(PauliTest $pauliTest)
    {
        $alatTesId = $pauliTest->alat_tes_id;

        DB::beginTransaction();
        try {
            // Delete associated PAULI questions that were generated for this configuration (if any)
            Question::where('pauli_test_id', $pauliTest->id)
                ->where('type', 'PAULI')
                ->delete();

            $pauliTest->delete();

            DB::commit();

            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('success', 'Konfigurasi Pauli Test dan soal terkait berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete PauliTest and its questions', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus konfigurasi Pauli: ' . $e->getMessage());
        }
    }

    /**
     * Persist Pauli questions as Question records
     *
     * If the total number of questions is large, dispatch a queued job instead.
     */
    private function persistPauliQuestions(PauliTest $pauliTest)
    {
        $totalQuestions = $pauliTest->total_columns * $pauliTest->pairs_per_column;

        // Safety: don't block request for very large numbers — queue it
        $QUEUE_THRESHOLD = 1000;

        if ($totalQuestions > $QUEUE_THRESHOLD) {
            // Dispatch as job
            GeneratePauliQuestions::dispatch($pauliTest->id);
            Log::info('Dispatched GeneratePauliQuestions job', ['pauli_test_id' => $pauliTest->id, 'total' => $totalQuestions]);
            return ['queued' => true, 'created' => 0];
        }

        // Create immediately
        $created = 0;
        DB::beginTransaction();
        try {
            for ($col = 0; $col < $pauliTest->total_columns; $col++) {
                for ($pair = 0; $pair < $pauliTest->pairs_per_column; $pair++) {
                    $top = rand(1, 9);
                    $bottom = rand(1, 9);

                    Question::create([
                        'alat_tes_id' => $pauliTest->alat_tes_id,
                        'pauli_test_id' => $pauliTest->id,
                        'type' => 'PAULI',
                        'question_text' => null,
                        'example_question' => null,
                        'instructions' => null,
                        'options' => null,
                        'correct_answer_index' => null,
                        'correct_answers' => null,
                        'ranking_category' => null,
                        'ranking_weight' => null,
                        'metadata' => json_encode([
                            'column' => $col + 1,
                            'pair_index' => $pair + 1,
                            'top' => $top,
                            'bottom' => $bottom,
                            'correct_sum' => $top + $bottom,
                        ]),
                    ]);

                    $created++;
                }
            }

            DB::commit();

            Log::info('Persisted Pauli questions synchronously', ['pauli_test_id' => $pauliTest->id, 'created' => $created]);

            return ['queued' => false, 'created' => $created];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to persist Pauli questions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * Tampilkan hasil test per konfigurasi
     * Route: /admin/pauli/{pauliTest}/results
     */
    public function results(PauliTest $pauliTest)
    {
        $results = $pauliTest->results()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.questions.index_pauli_result', compact('pauliTest', 'results'));
    }

    /**
     * Tampilkan detail hasil individual
     * Route: /admin/pauli/result/{resultId}
     */
    public function showResult($resultId)
    {
        $result = \App\Models\PauliResult::with(['user', 'pauliTest'])
            ->findOrFail($resultId);

        return view('admin.questions.show_pauli_result', compact('result'));
    }
}
