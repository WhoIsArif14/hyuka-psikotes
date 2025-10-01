<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index($alatTesId)
    {
        $alatTes = AlatTes::findOrFail($alatTesId);

        // Ubah dari ->get() ke ->paginate()
        $questions = $alatTes->questions()->paginate(10);

        return view('admin.questions.index', compact('alatTes', 'questions'));
    }

    public function create(AlatTes $alatTe) // Asumsikan Anda menggunakan Route Model Binding
    {
        // Pastikan Anda meneruskan ID Alat Tes ke view
        return view('admin.questions.create', ['alatTeId' => $alatTe->id]);
    }

    public function store(Request $request, $alat_tes)
    {
        $alatTes = AlatTes::findOrFail($alat_tes);

        $request->validate([
            'question' => 'required|string|max:255',
        ]);

        $alatTes->questions()->create([
            'question' => $request->question,
        ]);

        return redirect()->route('admin.alat-tes.questions.index', $alatTes->id)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function import(Request $request, $alat_tes)
    {
        $alatTes = AlatTes::findOrFail($alat_tes);

        // proses import file excel/csv
        // misal pakai maatwebsite/excel

        return redirect()->route('admin.alat-tes.questions.index', $alatTes->id)
            ->with('success', 'Soal berhasil diimport.');
    }
}
