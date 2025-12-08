<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use App\Models\RmibItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RmibQuestionController extends Controller
{
    /**
     * Show create form for RMIB questions
     */
    public function create($alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        // Get existing RMIB questions count
        $existingRmibCount = Question::where('alat_tes_id', $AlatTes->id)
            ->where('type', 'RMIB')
            ->count();

        // Get all RMIB items from master data
        $rmibItems = RmibItem::orderBy('item_number')->get();

        // Get interest areas
        $interestAreas = RmibItem::getInterestAreas();

        return view('admin.questions.create_rmib', compact('AlatTes', 'existingRmibCount', 'rmibItems', 'interestAreas'));
    }

    /**
     * Store RMIB questions (auto-generate 144 or manual single)
     */
    public function store(Request $request, $alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        // Check if auto-generate mode
        if ($request->has('auto_generate_rmib') && $request->auto_generate_rmib == '1') {
            return $this->autoGenerateRmib($request, $AlatTes);
        }

        // Manual single question creation
        return $this->storeManualRmib($request, $AlatTes);
    }

    /**
     * Auto-generate 144 RMIB questions from master data
     */
    private function autoGenerateRmib(Request $request, $AlatTes)
    {
        $request->validate([
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ]);

        DB::beginTransaction();

        try {
            // Get all RMIB items from master data
            $rmibItems = RmibItem::orderBy('item_number')->get();

            if ($rmibItems->isEmpty()) {
                return back()->with('error', '❌ Data RMIB belum ada di database. Jalankan seeder terlebih dahulu: php artisan db:seed --class=RmibItemSeeder');
            }

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($rmibItems as $item) {
                // Check if item already exists for this Alat Tes
                $exists = Question::where('alat_tes_id', $AlatTes->id)
                    ->where('rmib_item_id', $item->id)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Create Question linked to RMIB item
                Question::create([
                    'alat_tes_id' => $AlatTes->id,
                    'rmib_item_id' => $item->id,
                    'type' => 'RMIB',
                    'question_text' => $item->description,
                    'example_question' => $request->example_question,
                    'instructions' => $request->instructions,
                    'ranking_category' => $item->interest_area, // Store interest area
                    'ranking_weight' => $item->item_number, // Store item number
                    'options' => null, // RMIB uses ranking, not multiple choice
                    'correct_answer_index' => null,
                    'correct_answers' => null,
                    'image_path' => null,
                ]);

                $createdCount++;
            }

            DB::commit();

            $message = "✅ Berhasil membuat {$createdCount} item RMIB!";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} item sudah ada dan dilewati)";
            }

            Log::info('RMIB Auto-Generate Success', [
                'alat_tes_id' => $AlatTes->id,
                'created' => $createdCount,
                'skipped' => $skippedCount,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Auto-Generate Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal membuat item RMIB: ' . $e->getMessage());
        }
    }

    /**
     * Store manual single RMIB item
     */
    /**
     * Store manual single RMIB item
     */
    private function storeManualRmib(Request $request, $AlatTes)
    {
        // ✅ PERBAIKAN: Validasi fleksibel
        $rules = [
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        // Hanya require rmib_item_id di mode manual
        if (!$request->has('auto_generate_rmib') || $request->auto_generate_rmib != '1') {
            $rules['rmib_item_id'] = 'required|exists:rmib_items,id';
        }

        $request->validate($rules, [
            'rmib_item_id.required' => 'Silakan pilih item RMIB dari daftar',
            'rmib_item_id.exists' => 'Item RMIB tidak ditemukan',
            'question_image.max' => '⚠️ Ukuran gambar terlalu besar! Maksimal 5 MB.',
        ]);

        DB::beginTransaction();

        try {
            $rmibItem = RmibItem::findOrFail($request->rmib_item_id);

            // Check if already exists
            $exists = Question::where('alat_tes_id', $AlatTes->id)
                ->where('rmib_item_id', $rmibItem->id)
                ->exists();

            if ($exists) {
                return back()->with('error', '❌ Item RMIB #' . $rmibItem->item_number . ' sudah ada di Alat Tes ini.');
            }

            // Handle image upload (optional)
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Create Question
            $question = Question::create([
                'alat_tes_id' => $AlatTes->id,
                'rmib_item_id' => $rmibItem->id,
                'type' => 'RMIB',
                'question_text' => $rmibItem->description,
                'image_path' => $imagePath,
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
                'ranking_category' => $rmibItem->interest_area,
                'ranking_weight' => $rmibItem->item_number,
                'options' => null,
                'correct_answer_index' => null,
                'correct_answers' => null,
            ]);

            DB::commit();

            Log::info('RMIB Manual Create Success', [
                'alat_tes_id' => $AlatTes->id,
                'rmib_item_id' => $rmibItem->id,
                'item_number' => $rmibItem->item_number,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', '✅ Item RMIB #' . $rmibItem->item_number . ' (' . $rmibItem->interest_area_name . ') berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('RMIB Manual Store Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal menyimpan item RMIB: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for RMIB question
     */
    public function edit($alatTesId, $questionId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);
        $question = Question::with('rmibItem')->findOrFail($questionId);

        // Verify it's RMIB type
        if ($question->type !== 'RMIB') {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('error', '❌ Soal ini bukan tipe RMIB');
        }

        // Verify ownership
        if ($question->alat_tes_id != $AlatTes->id) {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('error', '❌ Item ini bukan milik Alat Tes ini');
        }

        $interestAreas = RmibItem::getInterestAreas();

        return view('admin.questions.edit_rmib', compact('AlatTes', 'question', 'interestAreas'));
    }

    /**
     * Update RMIB question
     */
    public function update(Request $request, $alatTesId, $questionId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);
        $question = Question::findOrFail($questionId);

        // Verify ownership and type
        if ($question->type !== 'RMIB' || $question->alat_tes_id != $AlatTes->id) {
            return back()->with('error', '❌ Item RMIB tidak valid');
        }

        $request->validate([
            'question_text' => 'nullable|string|max:1000',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'question_image.max' => '⚠️ Ukuran gambar terlalu besar! Maksimal 5 MB.',
        ]);

        DB::beginTransaction();

        try {
            // Handle image
            $imagePath = $question->image_path;
            if ($request->hasFile('question_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Update question
            $question->update([
                'question_text' => $request->question_text ?? ($question->rmibItem ? $question->rmibItem->description : $question->question_text),
                'image_path' => $imagePath,
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
            ]);

            DB::commit();

            Log::info('RMIB Update Success', [
                'alat_tes_id' => $AlatTes->id,
                'question_id' => $question->id,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', '✅ Item RMIB berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Update Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal memperbarui item RMIB: ' . $e->getMessage());
        }
    }

    /**
     * Delete RMIB question
     */
    public function destroy($alatTesId, $questionId)
    {
        try {
            DB::beginTransaction();

            $question = Question::findOrFail($questionId);

            // Verify ownership and type
            if ($question->type !== 'RMIB' || $question->alat_tes_id != $alatTesId) {
                return back()->with('error', '❌ Item RMIB tidak valid');
            }

            $itemNumber = $question->ranking_weight;

            // Delete image if exists
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }

            $question->delete();

            DB::commit();

            Log::info('RMIB Delete Success', [
                'alat_tes_id' => $alatTesId,
                'question_id' => $questionId,
                'item_number' => $itemNumber,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('success', '✅ Item RMIB #' . $itemNumber . ' berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Delete Error: ' . $e->getMessage());

            return back()->with('error', '❌ Gagal menghapus item RMIB: ' . $e->getMessage());
        }
    }
}
