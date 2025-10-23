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
     * PERBAIKAN TOTAL: Debug dan fix logic untuk PAPI
     */
    public function index($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);
        
        // Debug: Log informasi alat tes
        Log::info('QuestionController@index called', [
            'alat_tes_id' => $alat_te,
            'alat_tes_name' => $AlatTes->name,
            'alat_tes_slug' => $AlatTes->slug ?? 'NULL'
        ]);
        
        // PERBAIKAN: Cek PAPI berdasarkan nama jika slug tidak ada
        $isPapi = $this->checkIsPapi($AlatTes);
        
        Log::info('Is PAPI check result', [
            'is_papi' => $isPapi,
            'method' => 'checkIsPapi'
        ]);
        
        if ($isPapi) {
            // Ambil soal PAPI
            $questions = PapiQuestion::orderBy('item_number')->paginate(10);
            
            Log::info('PAPI questions loaded', [
                'total' => $questions->total(),
                'current_page' => $questions->currentPage(),
                'per_page' => $questions->perPage()
            ]);
            
            // Debug: Tampilkan 5 item pertama
            Log::info('First 5 PAPI questions', [
                'questions' => PapiQuestion::orderBy('item_number')->limit(5)->get()->toArray()
            ]);
        } else {
            // Ambil soal biasa
            $questions = $AlatTes->questions()->paginate(10);
            
            Log::info('Regular questions loaded', [
                'total' => $questions->total()
            ]);
        }

        return view('admin.questions.index', compact('AlatTes', 'questions'));
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
        
        // Method 3: Cek berdasarkan ID (fallback)
        // Ganti 1 dengan ID PAPI Kostick di database Anda
        if ($alatTes->id == 1) {
            Log::info('PAPI detected by ID', ['id' => $alatTes->id]);
            return true;
        }
        
        return false;
    }

    public function create($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);
        return view('admin.questions.create', ['alatTeId' => $AlatTes->id]);
    }

    /**
     * Menyimpan soal baru (Mencakup logika PAPI dan Umum).
     * PERBAIKAN: Menambahkan DB transaction dan error handling
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
            $rules['papi_item_number'] = 'required|integer|min:1|max:90|unique:papi_questions,item_number';
            $rules['role_a'] = 'required|string|max:1';
            $rules['need_a'] = 'required|string|max:1';
            $rules['role_b'] = 'required|string|max:1';
            $rules['need_b'] = 'required|string|max:1';
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
        // PERBAIKAN: Menggunakan DB transaction untuk memastikan data tersimpan
        if ($request->type === 'PAPIKOSTICK') {
            try {
                DB::beginTransaction();
                
                $papiQuestion = PapiQuestion::create([
                    'item_number' => $request->papi_item_number,
                    'statement_a' => $request->input('options.0.text'),
                    'statement_b' => $request->input('options.1.text'),
                    'role_a' => strtoupper($request->role_a),
                    'need_a' => strtoupper($request->need_a),
                    'role_b' => strtoupper($request->role_b),
                    'need_b' => strtoupper($request->need_b),
                ]);

                DB::commit();
                
                // PENTING: Log sukses dengan detail
                Log::info('PAPI question created successfully', [
                    'item_number' => $request->papi_item_number,
                    'id' => $papiQuestion->id,
                    'statement_a' => $papiQuestion->statement_a,
                    'statement_b' => $papiQuestion->statement_b
                ]);
                
                // PENTING: Cek apakah data benar-benar ada di database
                $verify = PapiQuestion::find($papiQuestion->id);
                Log::info('Verification after insert', [
                    'exists' => $verify ? 'YES' : 'NO',
                    'data' => $verify ? $verify->toArray() : null
                ]);

                return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                    ->with('success', 'Soal PAPI Kostick (Item ' . $request->papi_item_number . ') berhasil ditambahkan.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                
                // Log error
                Log::error('Failed to create PAPI question', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'item_number' => $request->papi_item_number
                ]);
                
                return back()->withInput()->withErrors(['error' => 'Gagal menyimpan soal PAPI: ' . $e->getMessage()]);
            }
        }

        // 4. LOGIKA PENYIMPANAN UMUM
        // PERBAIKAN: Menambahkan DB transaction
        try {
            DB::beginTransaction();
            
            // Handle Gambar Pertanyaan Utama
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            $questionData = [
                'alat_tes_id' => $alat_te,
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
            
            // Log sukses
            Log::info('Regular question created successfully', [
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
            
            // Log error
            Log::error('Failed to create question', [
                'error' => $e->getMessage(),
                'type' => $request->type
            ]);
            
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Menampilkan detail soal (Termasuk soal PAPI).
     */
    public function show($questionId)
    {
        // 1. Cek PAPI dulu
        $papiQuestion = PapiQuestion::find($questionId);
        
        if ($papiQuestion) {
            $question = $papiQuestion;
            $AlatTes = AlatTes::where('slug', 'papi-kostick')->first();
            return view('admin.questions.show_papi', compact('AlatTes', 'question'));
        }
        
        // 2. Jika bukan PAPI, cari di Question umum
        $question = Question::findOrFail($questionId);
        $AlatTes = $question->AlatTes;

        return view('admin.questions.show', compact('AlatTes', 'question'));
    }

    public function edit($questionId)
    {
        // 1. Cek PAPI
        $papiQuestion = PapiQuestion::find($questionId);

        if ($papiQuestion) {
            $AlatTes = AlatTes::where('slug', 'papi-kostick')->first();
            // PENTING: Arahkan ke form edit PAPI yang khusus
            return view('admin.questions.edit_papi', [
                'AlatTes' => $AlatTes,
                'question' => $papiQuestion // Kirimkan PapiQuestion Model
            ]);
        }
        
        // 2. Soal Umum
        $question = Question::findOrFail($questionId);
        $AlatTes = $question->AlatTes;

        return view('admin.questions.edit', compact('AlatTes', 'question'));
    }

    // =========================================================================
    // METODE UPDATE
    // =========================================================================
    
    /**
     * Mengupdate Soal (Mencakup logika PAPI dan Umum).
     * NOTE: Route harus mengirimkan AlatTes ID (untuk redirect) dan Question ID.
     */
    public function update(Request $request, $alat_te, $questionId) 
    {
        // 1. Cek PAPI
        $papiQuestion = PapiQuestion::find($questionId);

        if ($papiQuestion) {
            return $this->updatePapiQuestion($request, $papiQuestion, $alat_te);
        }

        // 2. Update Soal Umum
        return $this->updateGeneralQuestion($request, $questionId, $alat_te);
    }

    /**
     * Logika Update Soal PAPI
     */
    protected function updatePapiQuestion(Request $request, PapiQuestion $papiQuestion, $alat_te)
    {
        $questionId = $papiQuestion->id;
        
        $request->validate([
            // Rule unique harus mengabaikan ID soal yang sedang diedit
            'papi_item_number' => 'required|integer|min:1|max:90|unique:papi_questions,item_number,' . $questionId, 
            'statement_a' => 'required|string|max:500', 
            'statement_b' => 'required|string|max:500', 
            'role_a' => 'required|string|max:1', 
            'need_a' => 'required|string|max:1',
            'role_b' => 'required|string|max:1', 
            'need_b' => 'required|string|max:1', 
        ], [
            'papi_item_number.unique' => 'Nomor Soal PAPI ini sudah digunakan.',
        ]);

        try {
            DB::beginTransaction();
            
            $papiQuestion->update([
                'item_number' => $request->papi_item_number,
                'statement_a' => $request->statement_a,
                'statement_b' => $request->statement_b,
                'role_a' => strtoupper($request->role_a),
                'need_a' => strtoupper($request->need_a),
                'role_b' => strtoupper($request->role_b),
                'need_b' => strtoupper($request->need_b),
            ]);

            DB::commit();

            return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', 'Soal PAPI Kostick Item ' . $papiQuestion->item_number . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update PAPI question', ['error' => $e->getMessage(), 'id' => $questionId]);
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

                    // Logika untuk menghapus gambar yang ditandai dihapus di view:
                    // Anda perlu menambahkan hidden input di view untuk menandai penghapusan
                    // if ($request->input("options.{$index}.delete_image")) {
                    //    Storage::disk('public')->delete($oldPath);
                    //    $optionData['image_path'] = null;
                    // }

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
     * PERBAIKAN: Menambahkan DB transaction
     */
    public function destroy($questionId)
    {
        try {
            DB::beginTransaction();
            
            // 1. CEK APAKAH INI SOAL PAPI
            $papiQuestion = PapiQuestion::find($questionId);

            if ($papiQuestion) {
                $itemNumber = $papiQuestion->item_number;
                $papiQuestion->delete();
                
                DB::commit();
                
                // Log sukses
                Log::info('PAPI question deleted', [
                    'id' => $questionId,
                    'item_number' => $itemNumber
                ]);
                
                $alatTes = AlatTes::where('slug', 'papi-kostick')->first();
                $testId = $alatTes ? $alatTes->id : null; 

                return redirect()->route('admin.alat-tes.questions.index', $testId)
                    ->with('success', 'Soal PAPI Kostick Item ' . $itemNumber . ' berhasil dihapus.');
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
            
            // Log sukses
            Log::info('Question deleted', [
                'id' => $questionId
            ]);

            return redirect()->route('admin.alat-tes.questions.index', $testId)
                ->with('success', 'Soal berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error
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
        $sheet->setCellValue('J1', 'PAPI Role A');
        $sheet->setCellValue('K1', 'PAPI Need A');
        $sheet->setCellValue('L1', 'PAPI Role B');
        $sheet->setCellValue('M1', 'PAPI Need B');
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'questions_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}