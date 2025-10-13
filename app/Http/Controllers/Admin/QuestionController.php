<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);
        $questions = $AlatTes->questions()->paginate(10);

        return view('admin.questions.index', compact('AlatTes', 'questions'));
    }

    public function create($alat_te)
    {
        $AlatTes = AlatTes::findOrFail($alat_te);
        return view('admin.questions.create', ['alatTeId' => $AlatTes->id]);
    }

    public function store(Request $request, $alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);

        // 1. DEFINISI RULES VALIDASI
        $rules = [
            'type' => 'required|in:PILIHAN_GANDA,ESSAY,HAFALAN',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string';
        }

        if ($request->type === 'PILIHAN_GANDA') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        if ($request->type === 'HAFALAN') {
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
            // ✅ PERBAIKAN: Validasi untuk pertanyaan setelah hafalan
            $rules['question_text'] = 'required|string';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // 2. HANDLE GAMBAR PERTANYAAN UTAMA
        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = $request->file('question_image')->store('questions', 'public');
        }

        // 3. SIAPKAN DATA PERTANYAAN
        $questionData = [
            'test_id' => $alat_te,
            'type' => $request->type,
            'image_path' => $imagePath,
        ];

        if ($request->type === 'HAFALAN') {
            // ✅ PERBAIKAN: Simpan materi hafalan
            $questionData['memory_content'] = $request->memory_content;
            $questionData['memory_type'] = $request->memory_type;
            $questionData['duration_seconds'] = $request->duration_seconds;
            
            // ✅ PERBAIKAN: Simpan pertanyaan setelah hafalan
            $questionData['question_text'] = $request->question_text;

            // ✅ PERBAIKAN: Simpan opsi jawaban (seperti PILIHAN_GANDA)
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
                }

                $processedOptions[] = $optionData;
            }

            $questionData['options'] = json_encode($processedOptions);
            $questionData['correct_answer_index'] = $request->is_correct;
            
        } else {
            $questionData['question_text'] = $request->question_text;

            if ($request->type === 'PILIHAN_GANDA') {
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
                    }

                    $processedOptions[] = $optionData;
                }

                $questionData['options'] = json_encode($processedOptions);
                $questionData['correct_answer_index'] = $request->is_correct;
            }
        }

        // 5. SIMPAN KE DATABASE
        try {
            $question = Question::create($questionData);

            return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function show($question)
    {
        $question = Question::findOrFail($question);
        return view('admin.questions.show', compact('question'));
    }

    public function edit($question)
    {
        $question = Question::findOrFail($question);
        $AlatTes = $question->AlatTes;

        return view('admin.questions.edit', compact('AlatTes', 'question'));
    }

    public function update(Request $request, $question)
    {
        $questionModel = Question::findOrFail($question);

        $rules = [
            'type' => 'required|in:PILIHAN_GANDA,ESSAY,HAFALAN',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string';
        }

        if ($request->type === 'PILIHAN_GANDA') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        if ($request->type === 'HAFALAN') {
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
            // ✅ PERBAIKAN: Validasi untuk pertanyaan setelah hafalan
            $rules['question_text'] = 'required|string';
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'nullable|string';
            $rules['options.*.image_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Handle gambar pertanyaan
        $imagePath = $questionModel->image_path;
        if ($request->hasFile('question_image')) {
            // Hapus gambar lama
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('question_image')->store('questions', 'public');
        }

        $questionData = [
            'type' => $request->type,
            'image_path' => $imagePath
        ];

        if ($request->type === 'HAFALAN') {
            // ✅ PERBAIKAN: Simpan materi hafalan
            $questionData['memory_content'] = $request->memory_content;
            $questionData['memory_type'] = $request->memory_type;
            $questionData['duration_seconds'] = $request->duration_seconds;
            
            // ✅ PERBAIKAN: Simpan pertanyaan setelah hafalan
            $questionData['question_text'] = $request->question_text;

            // ✅ PERBAIKAN: Simpan opsi jawaban
            $processedOptions = [];
            $optionsData = $request->options;
            
            foreach ($optionsData as $index => $option) {
                $optionData = [
                    'text' => $option['text'] ?? '',
                    'index' => $option['index'] ?? $index,
                    'image_path' => $option['image_path'] ?? null, // Keep existing if not updated
                ];
                
                if ($request->hasFile("options.{$index}.image_file")) {
                    // Hapus gambar lama jika ada
                    if (isset($option['image_path'])) {
                        Storage::disk('public')->delete($option['image_path']);
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
            $questionData['question_text'] = $request->question_text;
            $questionData['memory_content'] = null;
            $questionData['memory_type'] = null;
            $questionData['duration_seconds'] = null;

            if ($request->type === 'PILIHAN_GANDA') {
                $processedOptions = [];
                $optionsData = $request->options;
                
                foreach ($optionsData as $index => $option) {
                    $optionData = [
                        'text' => $option['text'] ?? '',
                        'index' => $option['index'] ?? $index,
                        'image_path' => $option['image_path'] ?? null,
                    ];
                    
                    if ($request->hasFile("options.{$index}.image_file")) {
                        if (isset($option['image_path'])) {
                            Storage::disk('public')->delete($option['image_path']);
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
        }

        $questionModel->update($questionData);

        return redirect()->route('admin.alat-tes.questions.index', $questionModel->test_id)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy($question)
    {
        $questionModel = Question::findOrFail($question);
        $testId = $questionModel->test_id;
        
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

        return redirect()->route('admin.alat-tes.questions.index', $testId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    public function import(Request $request, $question)
    {
        $question = Question::findOrFail($question);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        // TODO: Implementasi import

        return redirect()->back()
            ->with('success', 'Soal berhasil diimport.');
    }

    public function downloadTemplate()
    {
        // TODO: Implementasi download template
        return response()->download(storage_path('templates/questions_template.xlsx'));
    }
}