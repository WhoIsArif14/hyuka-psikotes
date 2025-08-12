<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Menampilkan daftar soal untuk tes tertentu.
     */
    public function index(Test $test)
    {
        // Ambil semua soal beserta pilihan jawabannya untuk tes ini
        // Eager loading 'options' untuk menghindari N+1 query problem
        $questions = $test->questions()->with('options')->latest()->get();
        return view('admin.questions.index', compact('test', 'questions'));
    }

    /**
     * Menyimpan soal baru ke database.
     */
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'question_text' => 'required|string',
        ]);

        $test->questions()->create($request->all());

        return redirect()->route('admin.tests.questions.index', $test)->with('success', 'Soal baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit soal.
     */
    public function edit(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    /**
     * Mengupdate data soal di database.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
        ]);

        $question->update($request->all());

        return redirect()->route('admin.tests.questions.index', $question->test_id)->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Menghapus soal dari database.
     */
    public function destroy(Question $question)
    {
        // Simpan test_id untuk redirect sebelum menghapus
        $testId = $question->test_id;
        $question->delete();

        return redirect()->route('admin.tests.questions.index', $testId)->with('success', 'Soal berhasil dihapus.');
    }
}