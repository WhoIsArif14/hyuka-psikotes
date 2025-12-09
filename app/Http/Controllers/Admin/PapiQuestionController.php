<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use App\Models\PapiKostickItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PapiQuestionController extends Controller
{
    /**
     * Show form for creating PAPI questions
     */
    public function create($alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        // Get existing PAPI questions count for this specific Alat Tes
        $existingPapiCount = Question::where('alat_tes_id', $alatTesId)
            ->where('type', 'PAPIKOSTICK')
            ->count();

        // Get all PAPI items from the standard library
        $papiItems = PapiKostickItem::orderBy('item_number')->get();

        return view('admin.questions.create_papi', compact('AlatTes', 'existingPapiCount', 'papiItems'));
    }

    /**
     * Store PAPI questions (auto-generate 90 or manual)
     */
    public function store(Request $request, $alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        // Check if auto-generate is enabled
        if ($request->has('auto_generate_papi') && $request->auto_generate_papi == '1') {
            return $this->autoGeneratePapi($request, $AlatTes);
        }

        // Manual single question creation
        return $this->storeManualPapi($request, $AlatTes);
    }

    /**
     * Auto-generate 90 PAPI Kostick questions from existing items
     */
    private function autoGeneratePapi(Request $request, $AlatTes)
    {
        // ✅ Validasi HANYA untuk example_question dan instructions
        $request->validate([
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ]);

        DB::beginTransaction();

        try {
            // Get all 90 PAPI items from database
            $papiItems = PapiKostickItem::orderBy('item_number')->get();

            if ($papiItems->isEmpty()) {
                return back()->with('error', '❌ Tidak ada data PAPI Kostick di database. Silakan jalankan seeder terlebih dahulu.');
            }

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($papiItems as $papiItem) {
                // ✅ Check menggunakan ranking_weight untuk lebih akurat
                $existingQuestion = Question::where('alat_tes_id', $AlatTes->id)
                    ->where('type', 'PAPIKOSTICK')
                    ->where('ranking_weight', $papiItem->item_number)
                    ->first();

                if ($existingQuestion) {
                    $skippedCount++;
                    Log::info("Skipping PAPI item {$papiItem->item_number} - already exists");
                    continue;
                }

                // ✅ Create Question - metadata LANGSUNG array (tidak perlu json_encode)
                $question = Question::create([
                    'alat_tes_id' => $AlatTes->id,
                    'type' => 'PAPIKOSTICK',
                    'question_text' => "Item {$papiItem->item_number}",
                    'example_question' => $request->example_question,
                    'instructions' => $request->instructions,
                    'ranking_category' => null,
                    'ranking_weight' => $papiItem->item_number, // ✅ Simpan nomor item
                    'metadata' => [ // ✅ LANGSUNG ARRAY - Laravel auto-cast ke JSON
                        'papi_item_number' => $papiItem->item_number,
                        'statement_a' => $papiItem->statement_a,
                        'statement_b' => $papiItem->statement_b,
                        'role_a' => $papiItem->role_a,
                        'need_a' => $papiItem->need_a,
                        'role_b' => $papiItem->role_b,
                        'need_b' => $papiItem->need_b,
                    ],
                ]);

                $createdCount++;

                Log::info('PAPI Question Created', [
                    'id' => $question->id,
                    'item_number' => $papiItem->item_number,
                    'ranking_weight' => $question->ranking_weight,
                    'metadata_count' => count($question->metadata ?? []),
                ]);
            }

            DB::commit();

            if ($createdCount == 0) {
                return redirect()
                    ->route('admin.alat-tes.questions.index', $AlatTes->id)
                    ->with('warning', "⚠️ Semua soal PAPI sudah ada di Alat Tes ini. ({$skippedCount} soal dilewati)");
            }

            $message = "✅ Berhasil membuat {$createdCount} soal PAPI Kostick!";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} soal sudah ada sebelumnya)";
            }

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PAPI Auto-Generate Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', '❌ Gagal membuat soal PAPI: ' . $e->getMessage());
        }
    }

    /**
     * Store manual single PAPI question
     */
    private function storeManualPapi(Request $request, $AlatTes)
    {
        // ✅ Validasi hanya jika bukan auto-generate
        $request->validate([
            'papi_item_id' => 'required|exists:papi_kostick_items,id',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ], [
            'papi_item_id.required' => 'Silakan pilih soal PAPI dari daftar',
            'papi_item_id.exists' => 'Soal PAPI tidak ditemukan',
        ]);

        DB::beginTransaction();

        try {
            $papiItem = PapiKostickItem::findOrFail($request->papi_item_id);

            // ✅ Check menggunakan ranking_weight
            $existingQuestion = Question::where('alat_tes_id', $AlatTes->id)
                ->where('type', 'PAPIKOSTICK')
                ->where('ranking_weight', $papiItem->item_number)
                ->first();

            if ($existingQuestion) {
                return back()->with('error', '❌ Soal PAPI nomor ' . $papiItem->item_number . ' sudah ada di Alat Tes ini.');
            }

            // ✅ Create Question - metadata langsung array
            $question = Question::create([
                'alat_tes_id' => $AlatTes->id,
                'type' => 'PAPIKOSTICK',
                'question_text' => "Item {$papiItem->item_number}",
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
                'ranking_category' => null,
                'ranking_weight' => $papiItem->item_number,
                'metadata' => [ // ✅ LANGSUNG ARRAY
                    'papi_item_number' => $papiItem->item_number,
                    'statement_a' => $papiItem->statement_a,
                    'statement_b' => $papiItem->statement_b,
                    'role_a' => $papiItem->role_a,
                    'need_a' => $papiItem->need_a,
                    'role_b' => $papiItem->role_b,
                    'need_b' => $papiItem->need_b,
                ],
            ]);

            DB::commit();

            Log::info('PAPI Question Created (Manual)', [
                'id' => $question->id,
                'item_number' => $papiItem->item_number,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', '✅ Soal PAPI berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PAPI Manual Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', '❌ Gagal menyimpan soal PAPI: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for PAPI question
     */
    public function edit($alatTesId, $questionId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);
        $question = Question::findOrFail($questionId);

        if ($question->type !== 'PAPIKOSTICK') {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('error', '❌ Soal ini bukan tipe PAPI Kostick');
        }

        return view('admin.questions.edit_papi', compact('AlatTes', 'question'));
    }

    /**
     * Update PAPI question
     */
    public function update(Request $request, $alatTesId, $questionId)
    {
        $request->validate([
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ]);

        DB::beginTransaction();

        try {
            $question = Question::findOrFail($questionId);

            // Update Question
            $question->update([
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('success', '✅ Soal PAPI berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PAPI Update Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal memperbarui soal PAPI: ' . $e->getMessage());
        }
    }

    /**
     * Delete PAPI question
     */
    public function destroy($alatTesId, $questionId)
    {
        try {
            $question = Question::findOrFail($questionId);

            if ($question->type !== 'PAPIKOSTICK') {
                return back()->with('error', '❌ Soal ini bukan tipe PAPI Kostick');
            }

            $question->delete();

            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('success', '✅ Soal PAPI berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('PAPI Delete Error: ' . $e->getMessage());
            return back()->with('error', '❌ Gagal menghapus soal PAPI: ' . $e->getMessage());
        }
    }
}