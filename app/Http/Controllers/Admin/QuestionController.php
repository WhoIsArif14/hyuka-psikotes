<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Menampilkan halaman untuk mengelola soal & pilihan jawaban.
     */
    public function index(Test $test)
    {
        // Eager load relasi 'options' untuk efisiensi
        $questions = $test->questions()->with('options')->latest()->get();
        return view('admin.questions.index', compact('test', 'questions'));
    }

    /**
     * Menyimpan soal baru beserta pilihan jawaban dan gambar.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'nullable|string|max:255',
            'option_images' => 'nullable|array',
            'option_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'correct_option' => 'required|integer',
        ]);

        DB::transaction(function () use ($request, $test) {
            $questionImagePath = null;
            // 1. Simpan gambar pertanyaan jika ada
            if ($request->hasFile('question_image')) {
                // Simpan di storage/app/public/question-images
                $questionImagePath = $request->file('question_image')->store('public/question-images');
            }

            // 2. Buat record pertanyaan di database
            $question = $test->questions()->create([
                'question_text' => $request->question_text,
                'image_path' => $questionImagePath ? Storage::url($questionImagePath) : null,
            ]);

            // 3. Proses dan simpan setiap pilihan jawaban
            foreach ($request->options as $index => $optionText) {
                // Abaikan jika teks opsi kosong
                if (empty($optionText)) {
                    continue;
                }

                $optionImagePath = null;
                // Cek dan simpan gambar untuk opsi ini
                if ($request->hasFile("option_images.{$index}")) {
                    $optionImagePath = $request->file("option_images.{$index}")->store('public/option-images');
                }

                $question->options()->create([
                    'option_text' => $optionText,
                    'image_path' => $optionImagePath ? Storage::url($optionImagePath) : null,
                    // Set poin menjadi 1 jika ini adalah jawaban yang benar
                    'point' => ($index == $request->correct_option) ? 1 : 0,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Soal baru berhasil ditambahkan.');
    }

    /**
     * Menghapus soal beserta gambar-gambarnya.
     */
    public function destroy(Question $question)
    {
        DB::transaction(function () use ($question) {
            // Hapus gambar utama jika ada
            if ($question->image_path) {
                Storage::delete(str_replace('/storage', 'public', $question->image_path));
            }

            // Hapus gambar dari setiap opsi
            foreach ($question->options as $option) {
                if ($option->image_path) {
                    Storage::delete(str_replace('/storage', 'public', $option->image_path));
                }
            }
            
            // Hapus record soal (opsi akan terhapus otomatis karena cascade)
            $question->delete();
        });

        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }
}

