<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use App\Models\PapiQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Menampilkan daftar soal (Perlu membedakan Soal Umum vs Soal PAPI).
     */
    public function index(AlatTes $alat_te)
    {
        $AlatTes = $alat_te;

        // ✅ Soal umum: urutkan dari yang terlama (soal lama di atas)
        $questions = Question::where('alat_tes_id', $AlatTes->id)
            ->orderBy('id', 'asc')  // atau ->oldest()
            ->paginate(10);

        // ✅ Soal PAPI: tetap urutkan berdasarkan item_number
        $papiQuestions = PapiQuestion::where('alat_tes_id', $AlatTes->id)
            ->orderBy('item_number', 'asc')
            ->paginate(10);

        return view('admin.questions.index', [
            'AlatTes' => $AlatTes,
            'questions' => $questions,
            'papiQuestions' => $papiQuestions,
        ]);
    }

    /**
     * Helper method yang lebih robust untuk cek PAPI
     */
    private function checkIsPapi($alatTes)
    {
        // Method 1: Cek slug jika ada
        if (isset($alatTes->slug) && !empty($alatTes->slug)) {
            $slug = strtolower(trim($alatTes->slug));
            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                Log::info('PAPI detected by slug', ['slug' => $alatTes->slug]);
                return true;
            }
        }

        // Method 2: Cek nama
        if (isset($alatTes->name) && !empty($alatTes->name)) {
            $name = strtolower(trim($alatTes->name));
            if (str_contains($name, 'papi') || str_contains($name, 'kostick')) {
                Log::info('PAPI detected by name', ['name' => $alatTes->name]);
                return true;
            }
        }

        return false;
    }

    public function create($alat_te)
    {
        $alat_te = AlatTes::findOrFail($alat_te);
        return view('admin.questions.create', ['alatTeId' => $alat_te->id]);
    }

    // -------------------------------------------------------------------------
    // FUNGSI STORE (CREATE)
    // -------------------------------------------------------------------------

    /**
     * Menyimpan soal baru (Mencakup logika PAPI dan Umum).
     */
    public function store(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);
        $imagePath = null;
        $optionImagePaths = [];

        // 1. VALIDASI TIPE & DEFENISI RULES
        $rules = [
            'type' => ['required', Rule::in(['PILIHAN_GANDA', 'ESSAY', 'HAFALAN', 'PAPIKOSTICK'])],
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // 2. TENTUKAN LOGIKA VALIDASI
        if ($request->type === 'PAPIKOSTICK') {
            $rules['papi_item_number'] = [
                'required',
                'integer',
                'min:1',
                'max:90',
                Rule::unique('papi_questions', 'item_number'),
            ];

            $rules['options.0.text'] = 'required|string|max:500';
            $rules['options.1.text'] = 'required|string|max:500';
        } else {
            if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY' || $request->type === 'HAFALAN') {
                $rules['question_text'] = 'required|string';
            }

            if ($request->type === 'PILIHAN_GANDA' || $request->type === 'HAFALAN') {
                $rules['options'] = 'required|array|min:2';
                $rules['options.*.text'] = 'nullable|string|max:500';
                $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                $rules['is_correct'] = 'required|integer|min:0';
            }

            if ($request->type === 'HAFALAN') {
                $rules['memory_content'] = 'required|string';
                $rules['memory_type'] = 'required|in:TEXT,IMAGE';
                $rules['duration_seconds'] = 'required|integer|min:1';
            }
        }

        $request->validate($rules, [
            'papi_item_number.unique' => 'Nomor Soal PAPI ini sudah digunakan.',
            'options.0.text.required' => 'Pernyataan A (Opsi A) wajib diisi.',
            'options.1.text.required' => 'Pernyataan B (Opsi B) wajib diisi.',
        ]);

        // 3. LOGIKA PENYIMPANAN PAPI
        if ($request->type === 'PAPIKOSTICK') {
            try {
                DB::beginTransaction();

                // ✅ PERBAIKAN: Tambahkan alat_tes_id
                $papiQuestion = PapiQuestion::create([
                    'alat_tes_id' => $alat_te,  // ✅ TAMBAHKAN INI
                    'item_number' => $request->papi_item_number,
                    'statement_a' => $request->input('options.0.text'),
                    'statement_b' => $request->input('options.1.text'),

                    // SET SEMUA ROLE DAN NEED KE NULL, AKAN DIUPDATE NANTI
                    'role_a' => null,
                    'need_a' => null,
                    'role_b' => null,
                    'need_b' => null,
                ]);

                DB::commit();

                Log::info('PAPI question created successfully', [
                    'alat_tes_id' => $alat_te,
                    'item_number' => $request->papi_item_number,
                    'id' => $papiQuestion->id,
                ]);

                return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                    ->with('success', 'Soal PAPI Kostick (Item ' . $request->papi_item_number . ') berhasil ditambahkan.');
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to create PAPI question', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'item_number' => $request->papi_item_number,
                    'alat_tes_id' => $alat_te,
                ]);

                return back()->withInput()->withErrors(['error' => 'Gagal menyimpan soal PAPI: ' . $e->getMessage()]);
            }
        }

        // 4. LOGIKA PENYIMPANAN UMUM (PILIHAN_GANDA, ESSAY, HAFALAN)
        try {
            DB::beginTransaction();

            // Handle Gambar Pertanyaan Utama
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            $questionData = [
                'alat_tes_id' => $alat_te,
                'test_id' => null,  // ✅ TAMBAHKAN INI (atau isi dengan ID test jika ada)
                'type' => $request->type,
                'image_path' => $imagePath,
                'question_text' => $request->question_text ?? null,
                'memory_content' => $request->memory_content ?? null,
                'memory_type' => $request->memory_type ?? null,
                'duration_seconds' => $request->duration_seconds ?? null,
                'options' => null,
                'correct_answer_index' => null,
            ];

            // Handle Opsi (untuk PG dan Hafalan)
            if ($request->type === 'PILIHAN_GANDA' || $request->type === 'HAFALAN') {
                $processedOptions = [];
                $optionsData = $request->options;

                foreach ($optionsData as $index => $option) {
                    $optionData = [
                        'text' => $option['text'] ?? '',
                        'index' => $option['index'] ?? $index,
                        'image_path' => null,
                    ];

                    if ($request->hasFile("options.{$index}.image_file")) {
                        $file = $request->file("options.{$index}.image_file");
                        $optionImagePath = $file->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                        $optionImagePaths[] = $optionImagePath;
                    }

                    $processedOptions[] = $optionData;
                }

                $questionData['options'] = json_encode($processedOptions);
                $questionData['correct_answer_index'] = $request->is_correct;
            }

            // 5. SIMPAN KE DATABASE (Tabel Questions)
            $question = Question::create($questionData);

            DB::commit();

            Log::info('Regular question created successfully', [
                'alat_tes_id' => $alat_te,
                'type' => $request->type,
                'id' => $question->id
            ]);

            return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup: Hapus gambar jika penyimpanan gagal
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

            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // FUNGSI SHOW, EDIT, UPDATE, DESTROY
    // -------------------------------------------------------------------------

    /**
     * Menampilkan detail soal (Termasuk soal PAPI).
     */
    public function show($questionId)
    {
        // 1. Cek PAPI dulu
        $papiQuestion = PapiQuestion::find($questionId);

        if ($papiQuestion) {
            $question = $papiQuestion;
            // Mencari AlatTes PAPI untuk konteks breadcrumb/menu
            $alat_te = AlatTes::where('slug', 'papi-kostick')->first() ?? AlatTes::where('name', 'like', '%papi%kostick%')->first();

            if (!$alat_te) {
                $alat_te = new AlatTes(['id' => 0, 'name' => 'PAPI Kostick (Alat Tes Tidak Ditemukan)']);
            }

            return view('admin.questions.show_papi', compact('alat_te', 'question'));
        }

        // 2. Jika bukan PAPI, cari di Question umum
        $question = Question::findOrFail($questionId);
        $alat_te = $question->alatTes;

        return view('admin.questions.show', compact('alat_te', 'question'));
    }

    public function edit(AlatTes $alat_te, Question $question)
    {
        // ✅ TAMBAHKAN INI DI BARIS PERTAMA
        \Log::info('Edit method called', [
            'alat_te_id' => $alat_te->id,
            'question_id' => $question->id,
            'question_type' => $question->type
        ]);

        $AlatTes = $alat_te;
        $isPapi = $this->checkIsPapi($AlatTes);

        if ($isPapi) {
            $papiQuestion = PapiQuestion::find($question->id);

            if (!$papiQuestion) {
                return back()->with('error', 'Soal PAPI tidak ditemukan dengan ID: ' . $question->id);
            }

            return view('admin.questions.edit_papi', compact('AlatTes', 'papiQuestion'));
        } else {
            // ✅ TAMBAHKAN LOG SEBELUM RENDER VIEW
            \Log::info('Rendering edit view', [
                'view' => 'admin.questions.edit',
                'AlatTes_id' => $AlatTes->id,
                'question_id' => $question->id
            ]);

            return view('admin.questions.edit', compact('AlatTes', 'question'));
        }
    }

    /**
     * Mengupdate Soal Umum (Route PUT questions/{question}).
     */
    public function update(Request $request, AlatTes $alat_te, Question $question)
    {
        $isPapi = $this->checkIsPapi($alat_te);

        if ($isPapi) {
            return back()->with('error', 'Gunakan rute khusus untuk memperbarui soal PAPI Kostick.');
        }

        // Lanjutkan Update Soal Umum
        return $this->updateGeneralQuestion($request, $question->id, $alat_te->id);
    }

    /**
     * FUNGSI KHUSUS: Mengupdate Soal PAPI (Route PUT alat-tes/{alat_te}/questions/{papi_question}/update-papi).
     */
    public function updatePapi(Request $request, AlatTes $alat_te, PapiQuestion $papi_question)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'item_number' => [
                'required',
                'integer',
                'min:1',
                'max:90',
                Rule::unique('papi_questions', 'item_number')->ignore($papi_question->id),
            ],
            'statement_a' => 'required|string|max:500',
            'statement_b' => 'required|string|max:500',
            'role_a' => 'required|string|size:1|regex:/^[A-Z]$/',
            'need_a' => 'required|string|size:1|regex:/^[A-Z]$/',
            'role_b' => 'required|string|size:1|regex:/^[A-Z]$/',
            'need_b' => 'required|string|size:1|regex:/^[A-Z]$/',
        ], [
            'item_number.unique' => 'Nomor Soal PAPI ini sudah digunakan.',
            'role_a.regex' => 'Kunci A (Role) harus berupa 1 huruf kapital.',
            'need_a.regex' => 'Kunci A (Need) harus berupa 1 huruf kapital.',
            'role_b.regex' => 'Kunci B (Role) harus berupa 1 huruf kapital.',
            'need_b.regex' => 'Kunci B (Need) harus berupa 1 huruf kapital.',
        ]);

        try {
            DB::beginTransaction();

            // 2. Update Data
            $papi_question->update([
                'item_number' => $validated['item_number'],
                'statement_a' => $validated['statement_a'],
                'statement_b' => $validated['statement_b'],
                'role_a' => strtoupper($validated['role_a']),
                'need_a' => strtoupper($validated['need_a']),
                'role_b' => strtoupper($validated['role_b']),
                'need_b' => strtoupper($validated['need_b']),
            ]);

            DB::commit();

            return redirect()->route('admin.alat-tes.questions.index', $alat_te->id)
                ->with('success', 'Soal PAPI Kostick Item ' . $papi_question->item_number . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update PAPI question', ['error' => $e->getMessage(), 'id' => $papi_question->id]);
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui soal PAPI: ' . $e->getMessage()]);
        }
    }

    /**
     * Logika Update Soal Umum
     */
    protected function updateGeneralQuestion(Request $request, $questionId, $alat_te)
    {
        $questionModel = Question::findOrFail($questionId);

        // 2. Definisikan Rules Validasi (Hanya untuk tipe umum)
        $rules = [
            'type' => ['required', Rule::in(['PILIHAN_GANDA', 'ESSAY', 'HAFALAN'])],
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY' || $request->type === 'HAFALAN') {
            $rules['question_text'] = 'required|string';
        }

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'HAFALAN') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string|max:500';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        if ($request->type === 'HAFALAN') {
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // 3. Handle Gambar Pertanyaan
            $imagePath = $questionModel->image_path;
            if ($request->hasFile('question_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath); // Hapus gambar lama
                }
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            // 4. Proses Opsi (PILIHAN GANDA/HAFALAN)
            $questionData = [
                'type' => $request->type,
                'image_path' => $imagePath,
                'question_text' => $request->question_text ?? null,
                'memory_content' => $request->memory_content ?? null,
                'memory_type' => $request->memory_type ?? null,
                'duration_seconds' => $request->duration_seconds ?? null,
            ];

            if ($request->type === 'PILIHAN_GANDA' || $request->type === 'HAFALAN') {
                $processedOptions = [];
                $optionsData = $request->options;

                $oldOptions = json_decode($questionModel->options, true) ?? [];

                foreach ($optionsData as $index => $option) {
                    $oldPath = $oldOptions[$index]['image_path'] ?? null;

                    $optionData = [
                        'text' => $option['text'] ?? '',
                        'index' => $option['index'] ?? $index,
                        'image_path' => $oldPath,
                    ];

                    if ($request->hasFile("options.{$index}.image_file")) {
                        if ($oldPath) {
                            Storage::disk('public')->delete($oldPath);
                        }
                        $file = $request->file("options.{$index}.image_file");
                        $optionImagePath = $file->store('option_images', 'public');
                        $optionData['image_path'] = $optionImagePath;
                    }

                    $processedOptions[] = $optionData;
                }

                $questionData['options'] = json_encode($processedOptions);
                $questionData['correct_answer_index'] = $request->is_correct;
            } else {
                $questionData['options'] = null;
                $questionData['correct_answer_index'] = null;
            }

            // 5. Update Model
            $questionModel->update($questionData);

            DB::commit();

            return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update question', ['error' => $e->getMessage(), 'id' => $questionId]);
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus soal (Mencakup logika PAPI dan Umum).
     */
    public function destroy($questionId)
    {
        try {
            DB::beginTransaction();

            $testId = null;

            // 1. CEK APAKAH INI SOAL PAPI
            $papiQuestion = PapiQuestion::find($questionId);

            if ($papiQuestion) {
                $itemNumber = $papiQuestion->item_number;

                // Cari AlatTes PAPI untuk mendapatkan ID-nya
                $alatTes = AlatTes::where('slug', 'papi-kostick')->first();
                if (!$alatTes) {
                    $alatTes = AlatTes::where('name', 'like', '%papi%kostick%')->first();
                }

                $testId = $alatTes ? $alatTes->id : null;

                $papiQuestion->delete();

                DB::commit();

                Log::info('PAPI question deleted', [
                    'id' => $questionId,
                    'item_number' => $itemNumber
                ]);

                if ($testId) {
                    return redirect()->route('admin.alat-tes.questions.index', $testId)
                        ->with('success', 'Soal PAPI Kostick Item ' . $itemNumber . ' berhasil dihapus.');
                }
                return back()->withErrors(['error' => 'Gagal menghapus: Tidak dapat menemukan ID Alat Tes PAPI untuk redirect.']);
            }

            // 2. HAPUS SOAL UMUM
            $questionModel = Question::findOrFail($questionId);
            $testId = $questionModel->alat_tes_id;

            // Hapus gambar terkait
            if ($questionModel->image_path) {
                Storage::disk('public')->delete($questionModel->image_path);
            }

            // Hapus gambar opsi
            if ($questionModel->options) {
                $options = is_string($questionModel->options) ? json_decode($questionModel->options, true) : $questionModel->options;
                foreach ($options as $option) {
                    if (isset($option['image_path']) && $option['image_path']) {
                        Storage::disk('public')->delete($option['image_path']);
                    }
                }
            }

            $questionModel->delete();

            DB::commit();

            Log::info('Question deleted', [
                'id' => $questionId
            ]);

            return redirect()->route('admin.alat-tes.questions.index', $testId)
                ->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete question', [
                'error' => $e->getMessage(),
                'id' => $questionId
            ]);

            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Import soal dari Excel.
     */
    public function import(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        // NOTE: Implementasi import (memerlukan Maatwebsite/Laravel-Excel dan Importer Class)
        // try {
        //     Excel::import(new QuestionImport($alatTes->id), $request->file('file'));
        // } catch (\Exception $e) {
        //     return back()->with('error', 'Gagal import: ' . $e->getMessage());
        // }

        return redirect()->back()
            ->with('success', 'Soal berhasil diimport (Fitur import belum diimplementasikan).');
    }

    /**
     * Download template Excel untuk import.
     */
    public function downloadTemplate()
    {
        // Generate template Excel dinamis
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Tipe (PILIHAN_GANDA/ESSAY/HAFALAN/PAPIKOSTICK)');
        $sheet->setCellValue('B1', 'Teks Pertanyaan');
        $sheet->setCellValue('C1', 'Opsi A (Teks)');
        $sheet->setCellValue('D1', 'Opsi B (Teks)');
        $sheet->setCellValue('E1', 'Jawaban Benar (Index 0/1/2...)');
        $sheet->setCellValue('F1', 'Memory Content (Jika Hafalan)');
        $sheet->setCellValue('G1', 'Memory Type (TEXT/IMAGE)');
        $sheet->setCellValue('H1', 'Duration Seconds');
        $sheet->setCellValue('I1', 'PAPI Item Number');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'questions_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
