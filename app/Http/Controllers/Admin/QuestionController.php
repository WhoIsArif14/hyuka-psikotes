<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // PENTING: Parameter menggunakan $alat_te (singular dari AlatTes model)
    // Sesuai dengan Route Model Binding Laravel

    public function index($alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);
        $questions = $alatTes->questions()->paginate(10);

        return view('admin.questions.index', compact('alatTes', 'questions'));
    }

    public function create($alat_te)
    {
        $alatTes = AlatTes::findOrFail($alat_te);
        return view('admin.questions.create', ['alatTeId' => $alatTes->id]);
    }

    public function store(Request $request, $alat_te)
    {
        // Debug: Cek ID dan data
        \Log::info('Debug Store Question', [
            'alat_te_param' => $alat_te,
            'type' => gettype($alat_te),
            'exists_in_alat_tes' => \DB::table('alat_tes')->where('id', $alat_te)->exists(),
            'alat_tes_data' => \DB::table('alat_tes')->where('id', $alat_te)->first(),
            'request_all' => $request->all(),
        ]);

        $alatTes = AlatTes::findOrFail($alat_te);

        // ... validasi
        $rules = [
            'type' => 'required|in:PILIHAN_GANDA,ESSAY,HAFALAN',
            'image_path' => 'nullable|string',
        ];

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string';
        }

        if ($request->type === 'PILIHAN_GANDA') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'required|string';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        if ($request->type === 'HAFALAN') {
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $questionData = [
            'test_id' => $alat_te, // Pastikan ini INTEGER, bukan STRING
            'type' => $request->type,
            'image_path' => $request->image_path,
        ];

        // Debug sebelum insert
        \Log::info('Question Data Before Insert', $questionData);

        if ($request->type === 'HAFALAN') {
            $questionData['question_text'] = '';
            $questionData['memory_content'] = $request->memory_content;
            $questionData['memory_type'] = $request->memory_type;
            $questionData['duration_seconds'] = $request->duration_seconds;
        } else {
            $questionData['question_text'] = $request->question_text;

            if ($request->type === 'PILIHAN_GANDA') {
                $questionData['options'] = json_encode($request->options);
                $questionData['correct_answer_index'] = $request->is_correct;
            }
        }

        try {
            $question = Question::create($questionData);
            \Log::info('Question Created Successfully', ['id' => $question->id]);

            return redirect()->route('admin.alat-tes.questions.index', $alat_te)
                ->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Failed to Create Question', [
                'error' => $e->getMessage(),
                'data' => $questionData
            ]);

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
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
        $alatTes = $question->alatTes;

        return view('admin.questions.edit', compact('alatTes', 'question'));
    }

    public function update(Request $request, $question)
    {
        $questionModel = Question::findOrFail($question);

        $rules = [
            'type' => 'required|in:PILIHAN_GANDA,ESSAY,HAFALAN',
            'image_path' => 'nullable|string',
        ];

        if ($request->type === 'PILIHAN_GANDA' || $request->type === 'ESSAY') {
            $rules['question_text'] = 'required|string';
        }

        if ($request->type === 'PILIHAN_GANDA') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'required|string';
            $rules['is_correct'] = 'required|integer|min:0';
        }

        if ($request->type === 'HAFALAN') {
            $rules['memory_content'] = 'required|string';
            $rules['memory_type'] = 'required|in:TEXT,IMAGE';
            $rules['duration_seconds'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $questionData = [
            'type' => $request->type,
            'image_path' => $request->image_path
        ];

        if ($request->type === 'HAFALAN') {
            $questionData['question_text'] = '';
            $questionData['memory_content'] = $request->memory_content;
            $questionData['memory_type'] = $request->memory_type;
            $questionData['duration_seconds'] = $request->duration_seconds;
            $questionData['options'] = null;
            $questionData['correct_answer_index'] = null;
        } else {
            $questionData['question_text'] = $request->question_text;
            $questionData['memory_content'] = null;
            $questionData['memory_type'] = null;
            $questionData['duration_seconds'] = null;

            if ($request->type === 'PILIHAN_GANDA') {
                $questionData['options'] = json_encode($request->options);
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
