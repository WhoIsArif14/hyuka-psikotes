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

        // ✅ Soal PAPI Kostick
        $papiQuestions = Question::where('alat_tes_id', $AlatTes->id)
            ->where('type', 'PAPIKOSTICK')

            ->orderBy('ranking_weight', 'asc')
            ->paginate(10, ['*'], 'papi_page');

        // ✅ Soal RMIB
        $rmibQuestions = Question::where('alat_tes_id', $AlatTes->id)
            ->where('type', 'RMIB')
            ->orderBy('ranking_weight', 'asc')
            ->paginate(10, ['*'], 'rmib_page');

        // ✅ DETEKSI TIPE ALAT TES
        $alatTesType = $this->detectAlatTesType($AlatTes);

        return view('admin.questions.index', [
            'AlatTes' => $AlatTes,
            'questions' => $questions,
            'papiQuestions' => $papiQuestions,
            'rmibQuestions' => $rmibQuestions,
            'alatTesType' => $alatTesType, // ✅ PASS KE VIEW
            // Pastikan contoh soal & instruksi tersedia untuk partial atau include yang mungkin mengharapkannya
            'exampleQuestions' => $AlatTes->example_questions ?? [],
            'instructions' => $AlatTes->instructions ?? null,
        ]);
    }

    private function detectAlatTesType(AlatTes $alatTes)
    {
        $name = strtolower($alatTes->name);
        $slug = strtolower($alatTes->slug ?? '');

        // ✅ Deteksi berdasarkan nama atau slug
        if (str_contains($name, 'papi') || str_contains($slug, 'papi')) {
            return 'PAPI';
        }

        if (str_contains($name, 'rmib') || str_contains($slug, 'rmib')) {
            return 'RMIB';
        }

        // ✅ Deteksi berdasarkan soal yang sudah ada
        $questionTypes = Question::where('alat_tes_id', $alatTes->id)
            ->distinct()
            ->pluck('type')
            ->toArray();

        if (in_array('PAPIKOSTICK', $questionTypes)) {
            return 'PAPI';
        }

        if (in_array('RMIB', $questionTypes)) {
            return 'RMIB';
        }

        // ✅ Default: soal umum (bisa semua tipe)
        return 'GENERAL';
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

        return view('admin.questions.create', compact('AlatTes', 'existingCount'));
    }

    /**
     * Store general questions (Pilihan Ganda, Essay, Hafalan)
     * ❌ TIDAK TERMASUK PAPIKOSTICK & RMIB (ada di controller terpisah)
     */
    public function store(Request $request, $alat_te)
    {
        // ✅ DEBUGGING - Log semua data yang masuk
        Log::info('=== STORE QUESTION STARTED ===', [
            'type' => $request->type,
            'question_text' => $request->question_text,
            'options' => $request->options,
            'is_correct' => $request->is_correct,
            'all_data' => $request->except(['question_image', 'options.*.image_file'])
        ]);

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
            // ✅ PERBAIKAN: Question text bisa null jika ada gambar
            $rules['question_text'] = 'nullable|string|min:1';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string|max:500';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';

            if ($request->type === 'PILIHAN_GANDA') {
                // ✅ PERBAIKAN: Validasi is_correct harus ada dan valid
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
            'question_image.max' => '⚠️ Ukuran gambar pertanyaan terlalu besar! Maksimal 5 MB (5120 KB).',
            'question_image.image' => '⚠️ File yang diupload harus berupa gambar (JPG, PNG, GIF).',
            'question_image.mimes' => '⚠️ Format gambar tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.',

            'options.*.image_file.max' => '⚠️ Ukuran gambar opsi terlalu besar! Maksimal 5 MB (5120 KB) per opsi.',
            'options.*.image_file.image' => '⚠️ File opsi yang diupload harus berupa gambar.',
            'options.*.image_file.mimes' => '⚠️ Format gambar opsi tidak didukung.',

            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'question_text.min' => 'Teks pertanyaan minimal 1 karakter.',
            'options.required' => 'Minimal harus ada 2 opsi jawaban.',
            'options.min' => 'Minimal harus ada 2 opsi jawaban.',
            'is_correct.required' => '⚠️ Anda harus menandai satu opsi sebagai jawaban yang benar!',
            'is_correct.integer' => '⚠️ Jawaban benar harus berupa angka index opsi.',
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

        // ✅ VALIDASI
        try {
            $validated = $request->validate($rules, $messages);
            Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['question_image', 'options.*.image_file'])
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Handle question image
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
                Log::info('Question image uploaded', ['path' => $imagePath]);
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
                    // Consider opsi terisi jika ada teks ATAU ada file gambar yang diupload
                    $hasText = isset($option['text']) && trim($option['text']) !== '';
                    $hasFile = $request->hasFile("options.{$index}.image_file");

                    if (! $hasText && ! $hasFile) {
                        Log::info("Skipping empty option at index {$index} (no text and no image)");
                        continue;
                    }

                    $optionData = [
                        'text' => $hasText ? trim($option['text']) : null,
                        'index' => $index,
                        'image_path' => null,
                    ];

                    // Handle option image upload
                    if ($hasFile) {
                        $file = $request->file("options.{$index}.image_file");
                        $optionImagePath = $file->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                        $optionImagePaths[] = $optionImagePath;
                        Log::info("Option {$index} image uploaded", ['path' => $optionImagePath]);
                    }

                    $processedOptions[] = $optionData;
                }

                // ✅ VALIDASI: Minimal 2 opsi setelah di-filter
                if (count($processedOptions) < 2) {
                    throw new \Exception('Minimal harus ada 2 opsi jawaban yang diisi!');
                }

                $questionData['options'] = json_encode($processedOptions);
                Log::info('Processed options', ['count' => count($processedOptions), 'options' => $processedOptions]);

                // Set correct answers
                if ($request->type === 'PILIHAN_GANDA_KOMPLEKS') {
                    $correctAnswers = $request->input('correct_answers', []);
                    $questionData['correct_answers'] = json_encode(array_map('intval', $correctAnswers));
                    $questionData['correct_answer_index'] = null;
                    Log::info('Multiple correct answers set', ['answers' => $correctAnswers]);
                } else {
                    // ✅ PERBAIKAN: Pastikan is_correct ada dan valid
                    if (!isset($request->is_correct) && $request->is_correct !== 0) {
                        throw new \Exception('Anda harus memilih satu jawaban yang benar!');
                    }
                    $questionData['correct_answer_index'] = (int)$request->is_correct;
                    $questionData['correct_answers'] = null;
                    Log::info('Single correct answer set', ['index' => $request->is_correct]);
                }
            }

            // ✅ LOG DATA SEBELUM DISIMPAN
            Log::info('Question data to be saved', [
                'data' => array_merge($questionData, ['options' => json_decode($questionData['options'] ?? '[]', true)])
            ]);

            // Create question
            $question = Question::create($questionData);

            Log::info('✅ Question created successfully', [
                'alat_tes_id' => $alat_te,
                'type' => $request->type,
                'id' => $question->id,
                'ranking_category' => $request->ranking_category,
            ]);

            DB::commit();

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

            Log::error('❌ Failed to create question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type' => $request->type,
                'alat_tes_id' => $alat_te,
                'input' => $request->except(['question_image', 'options.*.image_file'])
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

        return view('admin.questions.edit', compact('alat_te', 'question'));
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

                    $hasText = isset($option['text']) && trim($option['text']) !== '';
                    $hasFile = $request->hasFile("options.{$index}.image_file");
                    $hasOldImage = !empty($oldPath);

                    // Skip option if it has no text, no new file, and no existing image
                    if (! $hasText && ! $hasFile && ! $hasOldImage) {
                        Log::info("Skipping empty option during update at index {$index}");
                        continue;
                    }

                    $optionData = [
                        'text' => $hasText ? trim($option['text']) : null,
                        'index' => $option['index'] ?? $index,
                        'image_path' => $oldPath,
                    ];

                    // Handle option image update
                    if ($hasFile) {
                        if ($oldPath) {
                            Storage::disk('public')->delete($oldPath);
                        }
                        $optionImagePath = $request->file("options.{$index}.image_file")
                            ->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                    }

                    $processedOptions[] = $optionData;
                }

                // ✅ VALIDASI: Minimal 2 opsi setelah update
                if (count($processedOptions) < 2) {
                    throw new \Exception('Minimal harus ada 2 opsi jawaban yang diisi!');
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

    // ============================================================================
    // 🎓 RMIB METHODS
    // ============================================================================

    /**
     * Show create form for RMIB questions
     */
    public function createRmib($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);

        // Count existing RMIB questions
        $existingRmibCount = Question::where('alat_tes_id', $AlatTes->id)
            ->where('type', 'RMIB')
            ->count();

        // Get RMIB items from database (assuming you have rmib_items table)
        // If you don't have this table yet, create it with:
        // php artisan make:model RmibItem -m
        $rmibItems = collect([]); // Empty collection if table doesn't exist

        // Uncomment this when you have rmib_items table:
        // $rmibItems = \App\Models\RmibItem::orderBy('item_number', 'asc')->get();

        return view('admin.questions.create-rmib', compact('AlatTes', 'existingRmibCount', 'rmibItems'));
    }

    /**
     * Store RMIB questions
     */
    public function storeRmib(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);

        // ✅ Check if auto-generate is enabled
        if ($request->has('auto_generate_rmib') && $request->auto_generate_rmib == 1) {
            return $this->autoGenerateRmib($request, $alat_te);
        }

        // ✅ Manual single RMIB item creation
        $rules = [
            'rmib_item_id' => 'required|exists:rmib_items,id',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ];

        $messages = [
            'rmib_item_id.required' => 'Pilih item RMIB terlebih dahulu.',
            'rmib_item_id.exists' => 'Item RMIB tidak valid.',
            'question_image.max' => '⚠️ Ukuran gambar terlalu besar! Maksimal 5 MB.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $rmibItem = \App\Models\RmibItem::findOrFail($request->rmib_item_id);

            // Check if item already exists
            $exists = Question::where('alat_tes_id', $alat_te)
                ->where('type', 'RMIB')
                ->where('ranking_weight', $rmibItem->item_number)
                ->exists();

            if ($exists) {
                return back()->withErrors(['error' => "⚠️ Item RMIB #{$rmibItem->item_number} sudah ada."]);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Create RMIB question
            $question = Question::create([
                'alat_tes_id' => $alat_te,
                'type' => 'RMIB',
                'question_text' => $rmibItem->description, // Activity/profession description
                'image_path' => $imagePath,
                'example_question' => $this->sanitizeNullable($request->example_question),
                'instructions' => $this->sanitizeNullable($request->instructions),
                'ranking_weight' => $rmibItem->item_number, // Use item_number as ranking_weight
                'ranking_category' => $rmibItem->interest_area, // Interest area as category

                // RMIB specific - 5 rating options (ranking scale)
                'options' => json_encode([
                    ['text' => 'Sangat Tidak Suka', 'index' => 0],
                    ['text' => 'Tidak Suka', 'index' => 1],
                    ['text' => 'Netral', 'index' => 2],
                    ['text' => 'Suka', 'index' => 3],
                    ['text' => 'Sangat Suka', 'index' => 4],
                ]),

                'correct_answer_index' => null, // No correct answer for RMIB
                'correct_answers' => null,
            ]);

            DB::commit();

            Log::info('RMIB question created', [
                'question_id' => $question->id,
                'rmib_item_id' => $rmibItem->id,
                'item_number' => $rmibItem->item_number,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', '✅ Item RMIB berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('Failed to create RMIB question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Gagal menyimpan item RMIB: ' . $e->getMessage()]);
        }
    }

    /**
     * Auto-generate all 144 RMIB items
     */
    private function autoGenerateRmib(Request $request, $alat_te)
    {
        $rules = [
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Check if rmib_items exist
            if (!class_exists(\App\Models\RmibItem::class)) {
                return back()->withErrors(['error' => '❌ Model RmibItem belum dibuat. Jalankan: php artisan make:model RmibItem -m']);
            }

            $rmibItems = \App\Models\RmibItem::orderBy('item_number', 'asc')->get();

            if ($rmibItems->count() == 0) {
                return back()->withErrors(['error' => '❌ Data RMIB tidak ditemukan di database. Jalankan: php artisan db:seed --class=RmibItemSeeder']);
            }

            // Check if already exists
            $existingCount = Question::where('alat_tes_id', $alat_te)
                ->where('type', 'RMIB')
                ->count();

            if ($existingCount > 0) {
                return back()->withErrors(['error' => "⚠️ Sudah ada {$existingCount} item RMIB. Hapus terlebih dahulu jika ingin generate ulang."]);
            }

            $created = 0;
            $ratingOptions = json_encode([
                ['text' => 'Sangat Tidak Suka', 'index' => 0],
                ['text' => 'Tidak Suka', 'index' => 1],
                ['text' => 'Netral', 'index' => 2],
                ['text' => 'Suka', 'index' => 3],
                ['text' => 'Sangat Suka', 'index' => 4],
            ]);

            foreach ($rmibItems as $item) {
                Question::create([
                    'alat_tes_id' => $alat_te,
                    'type' => 'RMIB',
                    'question_text' => $item->description,
                    'image_path' => null,
                    'example_question' => $this->sanitizeNullable($request->example_question),
                    'instructions' => $this->sanitizeNullable($request->instructions),
                    'ranking_weight' => $item->item_number,
                    'ranking_category' => $item->interest_area,
                    'options' => $ratingOptions,
                    'correct_answer_index' => null,
                    'correct_answers' => null,
                ]);

                $created++;
            }

            DB::commit();

            Log::info('RMIB auto-generated successfully', [
                'alat_tes_id' => $alat_te,
                'total_created' => $created,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', "✅ Berhasil generate {$created} item RMIB standar!");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to auto-generate RMIB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Gagal generate RMIB: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for RMIB question
     */
    public function editRmib(AlatTes $alat_te, Question $question)
    {
        // Verify it's RMIB question
        if ($question->type !== 'RMIB') {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('error', '❌ Ini bukan soal RMIB');
        }

        // Verify ownership
        if ($question->alat_tes_id != $alat_te->id) {
            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('error', '❌ Soal ini bukan milik Alat Tes ini');
        }

        $rmibItems = collect([]); // Or load from DB if available
        // $rmibItems = \App\Models\RmibItem::orderBy('item_number', 'asc')->get();

        return view('admin.questions.edit-rmib', compact('alat_te', 'question', 'rmibItems'));
    }

    /**
     * Update RMIB question
     */
    public function updateRmib(Request $request, AlatTes $alat_te, Question $question)
    {
        // Verify it's RMIB question
        if ($question->type !== 'RMIB') {
            return back()->with('error', '❌ Ini bukan soal RMIB');
        }

        // Verify ownership
        if ($question->alat_tes_id != $alat_te->id) {
            return back()->with('error', '❌ Soal ini bukan milik Alat Tes ini');
        }

        $rules = [
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'example_question' => 'nullable|string|max:5000',
            'instructions' => 'nullable|string|max:5000',
            'question_text' => 'nullable|string|max:500', // Allow editing activity description
        ];

        $messages = [
            'question_image.max' => '⚠️ Ukuran gambar terlalu besar! Maksimal 5 MB.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Handle image update
            $imagePath = $question->image_path;
            if ($request->hasFile('question_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('questions/rmib', 'public');
            }

            // Update question
            $question->update([
                'question_text' => $this->sanitizeNullable($request->question_text) ?? $question->question_text,
                'image_path' => $imagePath,
                'example_question' => $this->sanitizeNullable($request->example_question),
                'instructions' => $this->sanitizeNullable($request->instructions),
            ]);

            DB::commit();

            Log::info('RMIB question updated', [
                'question_id' => $question->id,
                'item_number' => $question->ranking_weight,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('success', '✅ Item RMIB berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update RMIB question', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Gagal memperbarui item RMIB: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete RMIB question
     */
    public function destroyRmib($alat_te, Question $question)
    {
        // Verify it's RMIB question
        if ($question->type !== 'RMIB') {
            return back()->with('error', '❌ Ini bukan soal RMIB');
        }

        try {
            DB::beginTransaction();

            // Delete image if exists
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }

            $question->delete();

            DB::commit();

            Log::info('RMIB question deleted', [
                'question_id' => $question->id,
                'item_number' => $question->ranking_weight,
            ]);

            return redirect()
                ->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', '✅ Item RMIB berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete RMIB question', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
            ]);

            return back()->with('error', '❌ Gagal menghapus item RMIB: ' . $e->getMessage());
        }
    }

// ============================================================================
// 📁 IMPORT/EXPORT METHODS
// ============================================================================

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
     * Update Alat Tes instructions (moved to Kelola Soal)
     */
    public function updateInstructions(Request $request, $alat_te)
    {
        $validated = $request->validate([
            'instructions' => 'nullable|string'
        ]);

        $AlatTes = AlatTes::findOrFail($alat_te);
        $AlatTes->instructions = $validated['instructions'] ?? null;
        $AlatTes->save();

        Log::info('Updated alat tes instructions', ['id' => $AlatTes->id]);

        return back()->with('success', 'Instruksi berhasil diperbarui.');
    }

    /**
     * Store example question (as JSON in AlatTes.example_questions)
     */
    public function storeExample(Request $request, $alat_te)
    {
        $validated = $request->validate([
            'type' => 'required|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'question' => 'nullable|string|max:1000',
            'options' => 'nullable|string',
            'correct' => 'nullable|integer',
            'correct_multiple' => 'nullable|string',
            'statement_a' => 'nullable|string|max:1000',
            'statement_b' => 'nullable|string|max:1000',
            'memory_content' => 'nullable|string',
            'memory_type' => 'nullable|in:TEXT,IMAGE',
            'duration_seconds' => 'nullable|integer|min:1',
            'explanation' => 'nullable|string|max:500',
        ]);

        $AlatTes = AlatTes::findOrFail($alat_te);
        $examples = $AlatTes->example_questions ?? [];

        $example = [
            'type' => $validated['type'],
            'question' => $validated['question'] ?? '',
            'explanation' => $validated['explanation'] ?? '',
        ];

        if (isset($validated['options'])) {
            $options = array_values(array_filter(array_map('trim', explode("\n", $validated['options']))));
            $example['options'] = $options;
        }

        if ($validated['type'] === 'PAPIKOSTICK') {
            $example['statement_a'] = $validated['statement_a'] ?? '';
            $example['statement_b'] = $validated['statement_b'] ?? '';
        }

        if ($validated['type'] === 'PILIHAN_GANDA_KOMPLEKS') {
        } elseif ($validated['type'] === 'PILIHAN_GANDA_KOMPLEKS') {
            $raw = $validated['correct_multiple'] ?? '';
            $answers = array_filter(array_map('trim', explode(',', $raw)), function ($v) {
                return $v !== '';
            });
            $example['correct_answers'] = array_map('intval', $answers);
        } elseif ($validated['type'] === 'HAFALAN') {
            $example['memory_content'] = $validated['memory_content'] ?? '';
            $example['memory_type'] = $validated['memory_type'] ?? 'TEXT';
            $example['duration_seconds'] = (int) ($validated['duration_seconds'] ?? 10);
        } elseif ($validated['type'] !== 'PAPIKOSTICK') {
            $example['correct_answer'] = is_null($validated['correct']) ? null : (int) $validated['correct'];
        } else {
            // For PILIHAN_GANDA, PAULI, BINARY etc.
            $example['correct_answer'] = isset($validated['correct']) ? (int) $validated['correct'] : null;
        }

        $examples[] = $example;
        $AlatTes->example_questions = $examples;
        $AlatTes->save();

        Log::info('Added example question', ['alat_tes_id' => $AlatTes->id, 'type' => $example['type']]);

        return back()->with('success', 'Contoh soal berhasil ditambahkan.');
    }

    /**
     * Update example question by index
     */
    public function updateExample(Request $request, $alat_te, $index)
    {
        $validated = $request->validate([
            'type' => 'required|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'question' => 'nullable|string|max:1000',
            'options' => 'nullable|string',
            'correct' => 'nullable|integer',
            'correct' => 'nullable|string',
            'correct_multiple' => 'nullable|string',
            'statement_a' => 'nullable|string|max:1000',
            'statement_b' => 'nullable|string|max:1000',
            'memory_content' => 'nullable|string',
            'memory_type' => 'nullable|in:TEXT,IMAGE',
            'duration_seconds' => 'nullable|integer|min:1',
            'explanation' => 'nullable|string|max:500',
        ]);

        $AlatTes = AlatTes::findOrFail($alat_te);
        $examples = $AlatTes->example_questions ?? [];
        $idx = (int) $index;
        if (!isset($examples[$idx])) {
            return back()->with('error', 'Contoh soal tidak ditemukan.');
        }

        $example = [
            'type' => $validated['type'],
            'question' => $validated['question'] ?? '',
            'explanation' => $validated['explanation'] ?? '',
        ];

        if (isset($validated['options'])) {
            $options = array_values(array_filter(array_map('trim', explode("\n", $validated['options']))));
            $example['options'] = $options;
        }

        if ($validated['type'] === 'PAPIKOSTICK') {
            $example['statement_a'] = $validated['statement_a'] ?? '';
            $example['statement_b'] = $validated['statement_b'] ?? '';
        }

        if ($validated['type'] === 'PILIHAN_GANDA_KOMPLEKS') {
            $raw = $validated['correct_multiple'] ?? '';
        } elseif ($validated['type'] === 'PILIHAN_GANDA_KOMPLEKS') {
            $raw = $validated['correct'] ?? '';
            $answers = array_filter(array_map('trim', explode(',', $raw)), function ($v) {
                return $v !== '';
            });
            $example['correct_answers'] = array_map('intval', $answers);
            $example['correct_answer'] = null;
        } elseif ($validated['type'] === 'HAFALAN') {
            $example['memory_content'] = $validated['memory_content'] ?? '';
            $example['memory_type'] = $validated['memory_type'] ?? 'TEXT';
            $example['duration_seconds'] = (int) ($validated['duration_seconds'] ?? 10);
        } elseif ($validated['type'] !== 'PAPIKOSTICK') {
            $example['correct_answer'] = is_null($validated['correct']) ? null : (int) $validated['correct'];
            // Hafalan can also have single/multiple answers from the checkbox form
            $raw = $validated['correct'] ?? '';
            $example['correct_answers'] = str_contains($raw, ',') ? array_map('intval', explode(',', $raw)) : null;
            $example['correct_answer'] = !str_contains($raw, ',') && $raw !== '' ? (int)$raw : null;
        } else {
            // For PILIHAN_GANDA, PAULI, BINARY etc.
            $example['correct_answer'] = isset($validated['correct']) && $validated['correct'] !== '' ? (int) $validated['correct'] : null;
            $example['correct_answers'] = null;
        }

        $examples[$idx] = $example;
        $AlatTes->example_questions = $examples;
        $AlatTes->save();

        Log::info('Updated example question', ['alat_tes_id' => $AlatTes->id, 'index' => $idx, 'type' => $example['type']]);

        return back()->with('success', 'Contoh soal berhasil diperbarui.');
    }

    /**
     * Delete example question by index
     */
    public function destroyExample($alat_te, $index)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);
        $examples = $AlatTes->example_questions ?? [];
        $idx = (int) $index;
        if (!isset($examples[$idx])) {
            return back()->with('error', 'Contoh soal tidak ditemukan.');
        }
        array_splice($examples, $idx, 1);
        $AlatTes->example_questions = $examples;
        $AlatTes->save();

        Log::info('Deleted example question', ['alat_tes_id' => $AlatTes->id, 'index' => $idx]);

        return back()->with('success', 'Contoh soal berhasil dihapus.');
    }
}
