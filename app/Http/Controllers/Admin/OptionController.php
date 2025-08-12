<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Menyimpan pilihan jawaban baru untuk soal tertentu.
     */
    public function store(Request $request, Question $question)
    {
        $request->validate([
            'option_text' => 'required|string',
            'point' => 'required|integer',
        ]);

        $question->options()->create($request->all());

        return redirect()->route('admin.tests.questions.index', $question->test_id)->with('success', 'Pilihan jawaban berhasil ditambahkan.');
    }

    /**
     * Menghapus pilihan jawaban.
     */
    public function destroy(Option $option)
    {
        $testId = $option->question->test_id;
        $option->delete();

        return redirect()->route('admin.tests.questions.index', $testId)->with('success', 'Pilihan jawaban berhasil dihapus.');
    }
}