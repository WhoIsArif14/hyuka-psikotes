<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionController extends Controller
{
    /**
     * Convert empty strings to null
     */
    private function sanitizeNullable($value)
    {
        if (is_string($value) && trim($value) === '') {
            return null;
        }
        return $value;
    }

    /**
     * Display question list for Alat Tes
     * Shows: General Questions, PAPI Questions, RMIB Questions
     */
    public function index(AlatTes $alat_te)
    {
        $AlatTes = $alat_te;

        // ✅ Soal Umum (Pilihan Ganda, Essay, Hafalan)
        $questions = Question::where('alat_tes_id', $AlatTes->id)
            ->whereIn('type', ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'ESSAY', 'HAFALAN'])
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'general_page');

        // ✅ Soal PAPI Kostick (dari table terpisah)
        $papiQuestions = \App\Models\PapiQuestion::where('alat_tes_id', $AlatTes->id)
            ->orderBy('item_number', 'asc')
            ->paginate(10, ['*'], 'papi_page');

        // ✅ Soal RMIB (dari table questions dengan type RMIB)
        $rmibQuestions = Question::where('alat_tes_id', $AlatTes->id)
            ->where('type', 'RMIB')
            ->orderBy('ranking_weight', 'asc') // ranking_weight = item_number
            ->paginate(10, ['*'], 'rmib_page');

        return view('admin.questions.index', [
            'AlatTes' => $AlatTes,
            'questions' => $questions,
            'papiQuestions' => $papiQuestions,
            'rmibQuestions' => $rmibQuestions,
        ]);
    }

    /**
     * Show create form for general questions
     * (Pilihan Ganda, Pilihan Ganda Kompleks, Essay, Hafalan)
     */
    public function create($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);

        // Count existing questions
        $existingCount = Question::where('alat_tes_id', $AlatTes->id)
            ->whereIn('type', ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'ESSAY', 'HAFALAN'])
            ->count();

        return view('admin.alat-tes.questions.create', compact('AlatTes', 'existingCount'));
    }

    /**
     * Store general questions (Pilihan Ganda, Essay, Hafalan)
     * ❌ TIDAK TERMASUK PAPIKOSTICK & RMIB (ada di controller terpisah)
     */
    public function store(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);
        $imagePath = null;
        $optionImagePaths = [];

        // ✅ VALIDASI - HANYA UNTUK TIPE UMUM
        $rules = [
            'type' => ['required', Rule::in(['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'ESSAY', 'HAFALAN'])],
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'ranking_category' => 'nullable|string|max:100',
            'ranking_weight' => 'nullable|integer|min:1|max:100',
        ];

        // Type-specific validation
        if ($request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string|min:1';
        } elseif ($request->type === 'PILIHAN_GANDA' || $request->type === 'PILIHAN_GANDA_KOMPLEKS') {
            $rules['question_text'] = 'nullable|string|min:1';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string|max:500';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';

            if ($request->type === 'PILIHAN_GANDA') {
                $rules['is_correct'] = 'required|integer|min:0';
            } else {
                $rules['correct_answers'] = 'required|array|min:1';
                $rules['correct_answers.*'] = 'integer|min:0';
            }
        } elseif ($request->type === 'HAFALAN') {
            $rules['question_text'] = 'nullable|string';
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string|max:500';
        }

        // Custom validation messages
        $messages = [
            'question_image.max' => '⚠️ Ukuran gambar pertanyaan terlalu besar! Maksimal 5 MB (5120 KB). Silakan kompres gambar terlebih dahulu.',
            'question_image.image' => '⚠️ File yang diupload harus berupa gambar (JPG, PNG, GIF).',
            'question_image.mimes' => '⚠️ Format gambar tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.',

            'options.*.image_file.max' => '⚠️ Ukuran gambar opsi terlalu besar! Maksimal 5 MB (5120 KB) per opsi.',
            'options.*.image_file.image' => '⚠️ File opsi yang diupload harus berupa gambar.',
            'options.*.image_file.mimes' => '⚠️ Format gambar opsi tidak didukung. Hanya JPG, PNG, GIF, dan WEBP yang diperbolehkan.',

            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'question_text.min' => 'Teks pertanyaan minimal 1 karakter.',
            'options.required' => 'Minimal harus ada 2 opsi jawaban.',
            'options.min' => 'Minimal harus ada 2 opsi jawaban.',
            'is_correct.required' => 'Anda harus menandai satu opsi sebagai jawaban yang benar.',
            'correct_answers.required' => 'Anda harus menandai minimal satu opsi sebagai jawaban yang benar.',
            'correct_answers.min' => 'Minimal ada 1 jawaban yang benar harus dipilih.',

            'memory_content.required' => 'Konten materi hafalan wajib diisi.',
            'memory_type.required' => 'Tipe konten hafalan harus dipilih (TEXT atau IMAGE).',
            'duration_seconds.required' => 'Durasi tampil materi hafalan wajib diisi.',
            'duration_seconds.min' => 'Durasi minimal adalah 1 detik.',

            'ranking_category.max' => 'Kategori maksimal 100 karakter.',
            'ranking_weight.min' => 'Bobot soal minimal adalah 1.',
            'ranking_weight.max' => 'Bobot soal maksimal adalah 100.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Handle question image
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            // Prepare question data
            $questionData = [
                'alat_tes_id' => $alat_te,
                'test_id' => null,
                'type' => $request->type,
                'image_path' => $imagePath,
                'question_text' => $this->sanitizeNullable($request->question_text),
                'example_question' => $this->sanitizeNullable($request->example_question),
                'instructions' => $this->sanitizeNullable($request->instructions),
                'memory_content' => $this->sanitizeNullable($request->memory_content),
                'memory_type' => $request->memory_type ?? null,
                'duration_seconds' => $request->duration_seconds ?? null,
                'ranking_category' => $this->sanitizeNullable($request->ranking_category),
                'ranking_weight' => $request->ranking_weight ?? 1,
                'options' => null,
                'correct_answer_index' => null,
                'correct_answers' => null,
            ];

            // Handle options for multiple choice and hafalan
            if (in_array($request->type, ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'HAFALAN'])) {
                $processedOptions = [];

                foreach ($request->options as $index => $option) {
                    $optionData = [
                        'text' => $option['text'] ?? '',
                        'index' => $option['index'] ?? $index,
                        'image_path' => null,
                    ];

                    // Handle option image upload
                    if ($request->hasFile("options.{$index}.image_file")) {
                        $file = $request->file("options.{$index}.image_file");
                        $optionImagePath = $file->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                        $optionImagePaths[] = $optionImagePath;
                    }

                    $processedOptions[] = $optionData;
                }

                $questionData['options'] = json_encode($processedOptions);

                // Set correct answers
                if ($request->type === 'PILIHAN_GANDA_KOMPLEKS') {
                    $correctAnswers = $request->input('correct_answers', []);
                    $questionData['correct_answers'] = json_encode(array_map('intval', $correctAnswers));
                    $questionData['correct_answer_index'] = null;
                } else {
                    $questionData['correct_answer_index'] = $request->is_correct;
                    $questionData['correct_answers'] = null;
                }
            }

            // Create question
            $question = Question::create($questionData);

            DB::commit();

            Log::info('Question created successfully', [
                'alat_tes_id' => $alat_te,
                'type' => $request->type,
                'id' => $question->id,
                'ranking_category' => $request->ranking_category,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', '✅ Soal berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded files on error
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            foreach ($optionImagePaths as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Failed to create question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type' => $request->type,
                'alat_tes_id' => $alat_te,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Gagal menyimpan soal: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for general question
     */
    public function edit(AlatTes $alat_te, Question $question)
    {
        Log::info('Edit method called', [
            'alat_te_id' => $alat_te->id,
            'question_id' => $question->id,
            'question_type' => $question->type
        ]);

        // ✅ Redirect ke controller yang sesuai berdasarkan tipe
        if ($question->type === 'PAPIKOSTICK') {
            return redirect()->route('admin.alat-tes.questions.papi.edit', [$alat_te->id, $question->id])
                ->with('info', 'Dialihkan ke halaman edit PAPI Kostick');
        }

        if ($question->type === 'RMIB') {
            return redirect()->route('admin.alat-tes.questions.rmib.edit', [$alat_te->id, $question->id])
                ->with('info', 'Dialihkan ke halaman edit RMIB');
        }

        // Verify ownership
        if ($question->alat_tes_id != $alat_te->id) {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('error', '❌ Soal ini bukan milik Alat Tes ini');
        }

        return view('admin.alat-tes.questions.edit', compact('alat_te', 'question'));
    }

    /**
     * Update general question
     */
    public function update(Request $request, AlatTes $alat_te, Question $question)
    {
        // ✅ Prevent updating PAPI & RMIB questions here
        if ($question->type === 'PAPIKOSTICK') {
            return back()->with('error', '❌ Gunakan route khusus untuk update soal PAPI Kostick.');
        }

        if ($question->type === 'RMIB') {
            return back()->with('error', '❌ Gunakan route khusus untuk update soal RMIB.');
        }

        // Verify ownership
        if ($question->alat_tes_id != $alat_te->id) {
            return back()->with('error', '❌ Soal ini bukan milik Alat Tes ini');
        }

        // Validation rules
        $rules = [
            'type' => ['required', Rule::in(['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'ESSAY', 'HAFALAN'])],
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'ranking_category' => 'nullable|string|max:100',
            'ranking_weight' => 'nullable|integer|min:1|max:100',
        ];

        // Type-specific validation
        if ($request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string|min:1';
        } elseif (in_array($request->type, ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS'])) {
            $rules['question_text'] = 'nullable|string|min:1';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string|max:500';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';

            if ($request->type === 'PILIHAN_GANDA') {
                $rules['is_correct'] = 'required|integer|min:0';
            } else {
                $rules['correct_answers'] = 'required|array|min:1';
            }
        } elseif ($request->type === 'HAFALAN') {
            $rules['question_text'] = 'nullable|string';
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
            $rules['options'] = 'required|array|min:2';
        }

        $messages = [
            'question_image.max' => '⚠️ Ukuran gambar terlalu besar! Maksimal 5 MB.',
            'options.*.image_file.max' => '⚠️ Ukuran gambar opsi terlalu besar! Maksimal 5 MB.',
            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'options.required' => 'Minimal harus ada 2 opsi jawaban.',
            'is_correct.required' => 'Pilih satu jawaban yang benar.',
            'correct_answers.required' => 'Pilih minimal satu jawaban yang benar.',
            'memory_content.required' => 'Konten hafalan wajib diisi.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Handle question image
            $imagePath = $question->image_path;
            if ($request->hasFile('question_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            // Prepare update data
            $questionData = [
                'type' => $request->type,
                'image_path' => $imagePath,
                'question_text' => $this->sanitizeNullable($request->question_text),
                'example_question' => $this->sanitizeNullable($request->example_question),
                'instructions' => $this->sanitizeNullable($request->instructions),
                'memory_content' => $this->sanitizeNullable($request->memory_content),
                'memory_type' => $request->memory_type ?? null,
                'duration_seconds' => $request->duration_seconds ?? null,
                'ranking_category' => $this->sanitizeNullable($request->ranking_category),
                'ranking_weight' => $request->ranking_weight ?? 1,
            ];

            // Handle options update
            if (in_array($request->type, ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'HAFALAN'])) {
                $processedOptions = [];
                $oldOptions = json_decode($question->options, true) ?? [];

                foreach ($request->options as $index => $option) {
                    $oldPath = $oldOptions[$index]['image_path'] ?? null;

                    $optionData = [
                        'text' => $option['text'] ?? '',
                        'index' => $option['index'] ?? $index,
                        'image_path' => $oldPath,
                    ];

                    // Handle option image update
                    if ($request->hasFile("options.{$index}.image_file")) {
                        if ($oldPath) {
                            Storage::disk('public')->delete($oldPath);
                        }
                        $optionImagePath = $request->file("options.{$index}.image_file")
                            ->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                    }

                    $processedOptions[] = $optionData;
                }

                $questionData['options'] = json_encode($processedOptions);

                // Set correct answers
                if ($request->type === 'PILIHAN_GANDA_KOMPLEKS') {
                    $correctAnswers = $request->input('correct_answers', []);
                    $questionData['correct_answers'] = json_encode(array_map('intval', $correctAnswers));
                    $questionData['correct_answer_index'] = null;
                } else {
                    $questionData['correct_answer_index'] = $request->is_correct;
                    $questionData['correct_answers'] = null;
                }
            } else {
                $questionData['options'] = null;
                $questionData['correct_answer_index'] = null;
                $questionData['correct_answers'] = null;
            }

            // Update question
            $question->update($questionData);

            DB::commit();

            Log::info('Question updated successfully', [
                'alat_tes_id' => $alat_te->id,
                'question_id' => $question->id,
                'type' => $request->type,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('success', '✅ Soal berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'question_id' => $question->id,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Gagal memperbarui soal: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete general question
     */
    public function destroy($alat_te, Question $question)
    {
        // ✅ Prevent deleting PAPI & RMIB here
        if ($question->type === 'PAPIKOSTICK') {
            return back()->with('error', '❌ Gunakan route khusus untuk hapus soal PAPI Kostick.');
        }

        if ($question->type === 'RMIB') {
            return back()->with('error', '❌ Gunakan route khusus untuk hapus soal RMIB.');
        }

        try {
            DB::beginTransaction();

            // Delete question image
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }

            // Delete option images
            if ($question->options) {
                $options = json_decode($question->options, true);
                if (is_array($options)) {
                    foreach ($options as $option) {
                        if (isset($option['image_path']) && $option['image_path']) {
                            Storage::disk('public')->delete($option['image_path']);
                        }
                    }
                }
            }

            $question->delete();

            DB::commit();

            Log::info('Question deleted successfully', [
                'question_id' => $question->id,
                'type' => $question->type,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', '✅ Soal berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete question', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
            ]);

            return back()->with('error', '❌ Gagal menghapus soal: ' . $e->getMessage());
        }
    }

    /**
     * Import questions from Excel
     */
    public function import(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat Excel (XLSX, XLS) atau CSV.',
            'file.max' => '⚠️ Ukuran file terlalu besar! Maksimal 5 MB (5120 KB).',
        ]);

        // TODO: Implement Excel import logic
        // You can use Maatwebsite/Laravel-Excel package
        // Example:
        // try {
        //     Excel::import(new QuestionImport($alatTes->id), $request->file('file'));
        //     return redirect()->back()->with('success', '✅ Soal berhasil diimport!');
        // } catch (\Exception $e) {
        //     return back()->with('error', '❌ Gagal import: ' . $e->getMessage());
        // }

        return redirect()
            ->back()
            ->with('info', 'ℹ️ Fitur import sedang dalam pengembangan.');
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set column headers
            $headers = [
                'A1' => 'Tipe Soal',
                'B1' => 'Teks Pertanyaan',
                'C1' => 'Contoh Soal',
                'D1' => 'Instruksi',
                'E1' => 'Opsi A',
                'F1' => 'Opsi B',
                'G1' => 'Opsi C',
                'H1' => 'Opsi D',
                'I1' => 'Jawaban Benar (Index)',
                'J1' => 'Jawaban Benar Multiple (0,1,2...)',
                'K1' => 'Memory Content',
                'L1' => 'Memory Type',
                'M1' => 'Duration Seconds',
                'N1' => 'Kategori',
                'O1' => 'Bobot',
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Add example row
            $sheet->setCellValue('A2', 'PILIHAN_GANDA');
            $sheet->setCellValue('B2', 'Siapa presiden pertama Indonesia?');
            $sheet->setCellValue('C2', 'Contoh: Siapa presiden kedua RI? Jawab: Soeharto');
            $sheet->setCellValue('D2', 'Pilih jawaban yang paling tepat');
            $sheet->setCellValue('E2', 'Soekarno');
            $sheet->setCellValue('F2', 'Soeharto');
            $sheet->setCellValue('G2', 'Habibie');
            $sheet->setCellValue('H2', 'Megawati');
            $sheet->setCellValue('I2', '0');
            $sheet->setCellValue('N2', 'SEJARAH');
            $sheet->setCellValue('O2', '1');

            // Auto-size columns
            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create temporary file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'template_soal_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($tempFile);

            return response()
                ->download($tempFile, $fileName)
                ->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to generate template', ['error' => $e->getMessage()]);
            return back()->with('error', '❌ Gagal membuat template: ' . $e->getMessage());
        }
    }

    /**
     * Store example question
     */
    public function storeExample(Request $request, $alat_te)
    {
        // TODO: Implement if you have separate example_questions table
        return back()->with('info', 'ℹ️ Fitur ini belum diimplementasikan.');
    }

    /**
     * Delete example question
     */
    public function destroyExample($alat_te, $example)
    {
        // TODO: Implement if you have separate example_questions table
        return back()->with('info', 'ℹ️ Fitur ini belum diimplementasikan.');
    }
}
