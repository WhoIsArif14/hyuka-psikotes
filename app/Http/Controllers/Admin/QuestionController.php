<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Menampilkan halaman untuk mengelola soal.
     */
    public function index(Test $test)
    {
        $questions = $test->questions()->with('options')->latest()->get();
        return view('admin.questions.index', compact('test', 'questions'));
    }

    /**
     * Menyimpan soal baru ke database berdasarkan tipenya.
     */
    public function store(Request $request, Test $test)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:multiple_choice,image_upload',
        ]);

        $questionData = [
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
        ];

        if ($request->hasFile('question_image')) {
            // PERBAIKAN 1: Tentukan disk 'public'
            $path = $request->file('question_image')->store('question-images', 'public');
            $questionData['image_path'] = $path;
        }

        $question = $test->questions()->create($questionData);

        if ($validated['type'] === 'multiple_choice') {
            $request->validate([
                'options' => 'required|array|min:1',
                'options.*' => 'nullable|string|max:255',
                'option_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'correct_option' => 'required|integer',
            ]);

            foreach ($request->options as $index => $optionText) {
                if (!empty($optionText) || $request->hasFile("option_images.{$index}")) {
                    $optionData = [
                        'option_text' => $optionText,
                        'point' => ($index == $request->correct_option) ? 1 : 0,
                    ];

                    if ($request->hasFile("option_images.{$index}")) {
                        // PERBAIKAN 2: Tentukan disk 'public'
                        $path = $request->file("option_images.{$index}")->store('option-images', 'public');
                        $optionData['image_path'] = $path;
                    }

                    $question->options()->create($optionData);
                }
            }
        }

        return redirect()->back()->with('success', 'Soal baru berhasil ditambahkan.');
    }

    public function import(Request $request, Test $test)
    {
        $request->validate([
            'questions_file' => 'required|mimes:xlsx,xls',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 1. Simpan file-file gambar terlebih dahulu (jika ada)
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $file->store('question-images', 'public');
            }
        }

        // 2. Proses file Excel
        $filePath = $request->file('questions_file')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Mulai transaksi database untuk memastikan semua soal masuk atau tidak sama sekali
        DB::transaction(function () use ($sheet, $highestRow, $test) {
            // Loop dimulai dari baris 2 untuk melewati header
            for ($row = 2; $row <= $highestRow; $row++) {
                $questionText = $sheet->getCell('A' . $row)->getValue();

                // Hanya proses jika kolom pertanyaan tidak kosong
                if (!empty($questionText)) {
                    $imageData = $sheet->getCell('H' . $row)->getValue();

                    // Buat Soal Baru
                    $question = $test->questions()->create([
                        'question_text' => $questionText,
                        'image_path' => $imageData ? 'question-images/' . $imageData : null,
                        'type' => 'multiple_choice'
                    ]);

                    // Buat Pilihan Jawaban
                    $options = [];
                    $kunciJawaban = strtolower($sheet->getCell('G' . $row)->getValue());

                    // Looping kolom B (opsi_a) sampai F (opsi_e)
                    $optionColumns = ['B', 'C', 'D', 'E', 'F'];
                    $optionChars = ['a', 'b', 'c', 'd', 'e'];

                    foreach ($optionColumns as $index => $col) {
                        $optionText = $sheet->getCell($col . $row)->getValue();
                        if (!empty($optionText)) {
                            $options[] = [
                                'option_text' => $optionText,
                                'point' => ($optionChars[$index] == $kunciJawaban) ? 1 : 0,
                            ];
                        }
                    }

                    if (!empty($options)) {
                        $question->options()->createMany($options);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Soal berhasil diimpor dari file Excel!');
    }

    /**
     * Menghapus soal dari database.
     */
    public function destroy(Question $question)
    {
        $testId = $question->test_id;
        $question->delete();

        return redirect()->route('admin.tests.questions.index', $testId)->with('success', 'Soal berhasil dihapus.');
    }
}
