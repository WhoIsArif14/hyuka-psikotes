<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RmibQuestionController extends Controller
{
    /**
     * Show form for creating RMIB questions
     */
    public function create($alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        // Get existing RMIB questions count
        $existingRmibCount = Question::where('alat_tes_id', $alatTesId)
            ->where('type', 'RMIB')
            ->count();

        // Get interest areas
        $interestAreas = $this->getRmibInterestAreas();

        return view('admin.alat-tes.questions.create_rmib', compact('AlatTes', 'existingRmibCount', 'interestAreas'));
    }

    /**
     * Store RMIB question
     */
    public function store(Request $request, $alatTesId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);

        $request->validate([
            'rmib_item_number' => 'required|integer|min:1|max:144',
            'rmib_interest_area' => 'required|string|in:OUTDOOR,MECHANICAL,COMPUTATIONAL,SCIENTIFIC,PERSONAL_CONTACT,AESTHETIC,LITERARY,MUSICAL,SOCIAL_SERVICE,CLERICAL,PRACTICAL,MEDICAL',
            'question_text' => 'required|string|max:1000',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ], [
            'rmib_item_number.required' => 'Nomor item RMIB wajib diisi',
            'rmib_interest_area.required' => 'Bidang minat wajib dipilih',
            'question_text.required' => 'Deskripsi aktivitas wajib diisi',
        ]);

        DB::beginTransaction();

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Create Question
            $question = Question::create([
                'alat_tes_id' => $AlatTes->id,
                'type' => 'RMIB',
                'question_text' => $request->question_text,
                'question_image' => $imagePath,
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
                'ranking_category' => $request->rmib_interest_area, // Store interest area in ranking_category
                'ranking_weight' => 1,
            ]);

            // Create 5 rating options (Sangat Tidak Suka to Sangat Suka)
            $ratingOptions = [
                'Sangat Tidak Suka',
                'Tidak Suka',
                'Netral',
                'Suka',
                'Sangat Suka',
            ];

            foreach ($ratingOptions as $index => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'option_index' => $index,
                    'is_correct' => false, // RMIB has no correct answer
                    'score_value' => $index + 1, // 1-5 scoring
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.alat-tes.questions.index', $AlatTes->id)
                ->with('success', '✅ Soal RMIB berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Store Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal menyimpan soal RMIB: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for RMIB question
     */
    public function edit($alatTesId, $questionId)
    {
        $AlatTes = AlatTes::findOrFail($alatTesId);
        $question = Question::with('options')->findOrFail($questionId);

        if ($question->type !== 'RMIB') {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('error', '❌ Soal ini bukan tipe RMIB');
        }

        $interestAreas = $this->getRmibInterestAreas();

        return view('admin.alat-tes.questions.edit_rmib', compact('AlatTes', 'question', 'interestAreas'));
    }

    /**
     * Update RMIB question
     */
    public function update(Request $request, $alatTesId, $questionId)
    {
        $request->validate([
            'rmib_item_number' => 'required|integer|min:1|max:144',
            'rmib_interest_area' => 'required|string|in:OUTDOOR,MECHANICAL,COMPUTATIONAL,SCIENTIFIC,PERSONAL_CONTACT,AESTHETIC,LITERARY,MUSICAL,SOCIAL_SERVICE,CLERICAL,PRACTICAL,MEDICAL',
            'question_text' => 'required|string|max:1000',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $question = Question::findOrFail($questionId);

            // Handle image upload
            if ($request->hasFile('question_image')) {
                // Delete old image if exists
                if ($question->question_image) {
                    \Storage::disk('public')->delete($question->question_image);
                }
                $question->question_image = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Update Question
            $question->update([
                'question_text' => $request->question_text,
                'example_question' => $request->example_question,
                'instructions' => $request->instructions,
                'ranking_category' => $request->rmib_interest_area,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.alat-tes.questions.index', $alatTesId)
                ->with('success', '✅ Soal RMIB berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RMIB Update Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ Gagal memperbarui soal RMIB: ' . $e->getMessage());
        }
    }

    /**
     * Get RMIB interest areas
     */
    private function getRmibInterestAreas()
    {
        return [
            'OUTDOOR' => '1. Outdoor (Alam Terbuka)',
            'MECHANICAL' => '2. Mechanical (Mekanik)',
            'COMPUTATIONAL' => '3. Computational (Komputasi)',
            'SCIENTIFIC' => '4. Scientific (Ilmiah)',
            'PERSONAL_CONTACT' => '5. Personal Contact (Kontak Personal)',
            'AESTHETIC' => '6. Aesthetic (Estetika
            )',
            'LITERARY' => '7. Literary (Sastra)',
            'MUSICAL' => '8. Musical (Musik)',
            'SOCIAL_SERVICE' => '9. Social Service (Layanan Sosial)',
            'CLERICAL' => '10. Clerical (Administrasi)',
            'PRACTICAL' => '11. Practical (Praktis)',
            'MEDICAL' => '12. Medical (Medis)',
        ];
    }
}
