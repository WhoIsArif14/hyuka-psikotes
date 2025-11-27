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

        return view('admin.alat-tes.questions.create_papi', compact('AlatTes', 'existingPapiCount', 'papiItems'));
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

            foreach ($papiItems as $papiItem) {
                // Check if this item already exists for this Alat Tes
                $existingQuestion = Question::where('alat_tes_id', $AlatTes->id)
                    ->where('type', 'PAPIKOSTICK')
                    ->where('question_text', 'LIKE', "Item {$papiItem->item_number}%")
                    ->first();

                if ($existingQuestion) {
                    continue; // Skip if already exists
                }

                // Create Question record
                $question = Question::create([
                    'alat_tes_id' => $AlatTes->id,
                    'type' => 'PAPIKOSTICK',
                    'question_text' => "Item {$papiItem->item_number}",
                    'example_question' => $request->example_question,
                    'instructions' => $request->instructions,
                    'ranking_category' => null, // PAPI tidak pakai ranking
                    'ranking_weight' => null,
                ]);

                // Link to existing PAPI item (or create relation)
                // Option 1: Store reference to PapiKostickItem
                $question->papi_kostick_item_id = $papiItem->id; // Jika ada kolom ini
                $question->save();

                // Option 2: Store as JSON metadata
                $question->metadata = json_encode([
                    'papi_item_number' => $papiItem->item_number,
                    'statement_a' => $papiItem->statement_a,
                    'statement_b' => $papiItem->statement_b,
                    'aspect_a' => $papiItem->aspect_a,
                    'aspect_b' => $papiItem->aspect_b,
                ]);
                $question->save();

                $createdCount++;
            }

            DB::commit();

            if ($createdCount == 0) {
                return redirect()
                    ->route('admin.alat-tes.questions.index', $AlatTes->id)
                    ->with('warning', '⚠️ Semua soal PAPI sudah ada di Alat Tes ini.');
            }

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', "✅ Berhasil membuat {$createdCount} soal PAPI Kostick secara otomatis!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PAPI Auto-Generate Error: ' . $e->getMessage());
            
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

            // Check if already exists
            $existingQuestion = Question::where('alat_tes_id', $AlatTes->id)
                ->where('type', 'PAPIKOSTICK')
                ->where('question_text', 'LIKE', "Item {$papiItem->item_number}%")
                ->first();

            if ($existingQuestion) {
                return back()->with('error', '❌ Soal PAPI nomor ' . $papiItem->item_number . ' sudah ada di Alat Tes ini.');
            }

            // Create Question record
            $question = Question::create([
                'alat_tes_id' => $AlatTes->id,
                'type' => 'PAPIKOSTICK',
                'question_text' => "Item {$papiItem->item_number}",
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
                'ranking_category' => null,
                'ranking_weight' => null,
                'papi_kostick_item_id' => $papiItem->id, // Reference to PAPI item
            ]);

            // Store as metadata if needed
            $question->metadata = json_encode([
                'papi_item_number' => $papiItem->item_number,
                'statement_a' => $papiItem->statement_a,
                'statement_b' => $papiItem->statement_b,
                'aspect_a' => $papiItem->aspect_a,
                'aspect_b' => $papiItem->aspect_b,
            ]);
            $question->save();

            DB::commit();

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', '✅ Soal PAPI berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PAPI Manual Store Error: ' . $e->getMessage());
            
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

        // Get PAPI item reference
        $papiItem = null;
        if ($question->papi_kostick_item_id) {
            $papiItem = PapiKostickItem::find($question->papi_kostick_item_id);
        }

        return view('admin.alat-tes.questions.edit_papi', compact('AlatTes', 'question', 'papiItem'));
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

            // Note: Statement A/B tidak bisa diubah karena mengacu ke PapiKostickItem
            // Jika ingin mengubah, harus edit di PapiKostickItemController

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
}