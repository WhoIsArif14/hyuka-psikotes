<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PauliTest;
use App\Models\AlatTes;
use Illuminate\Http\Request;

class PauliTestController extends Controller
{
    /**
     * Tampilkan daftar Pauli Test untuk Alat Tes tertentu
     * Route: /admin/alat-tes/{alatTesId}/pauli
     */
    public function index($alatTesId)
    {
        $alatTes = AlatTes::findOrFail($alatTesId);
        
        $pauliTests = PauliTest::where('alat_tes_id', $alatTesId)
            ->with('results')
            ->paginate(10);

        return view('questions.index', compact('alatTes', 'pauliTests'));
    }

    /**
     * Form create Pauli Test
     */
    public function create(Request $request)
    {
        $alatTesId = $request->get('alat_tes_id');
        
        if (!$alatTesId) {
            return redirect()->back()->with('error', 'Alat Tes ID tidak ditemukan');
        }

        $alatTes = AlatTes::findOrFail($alatTesId);
        
        return view('questions.create_pauli', compact('alatTes'));
    }

    /**
     * Store Pauli Test
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alat_tes_id' => 'required|exists:alat_tes,id',
            'total_columns' => 'required|integer|min:1|max:60',
            'pairs_per_column' => 'required|integer|min:1|max:60',
            'time_per_column' => 'required|integer|min:10|max:300',
        ]);

        PauliTest::create($validated);

        return redirect()
            ->route('admin.alat-tes.questions.index', $validated['alat_tes_id'])
            ->with('success', 'Konfigurasi Pauli Test berhasil dibuat!');
    }

    /**
     * Form edit Pauli Test
     */
    public function edit(PauliTest $pauliTest)
    {
        $alatTes = $pauliTest->alatTes;
        return view('questions.edit_pauli', compact('pauliTest', 'alatTes'));
    }

    /**
     * Update Pauli Test
     */
    public function update(Request $request, PauliTest $pauliTest)
    {
        $validated = $request->validate([
            'alat_tes_id' => 'required|exists:alat_tes,id',
            'total_columns' => 'required|integer|min:1|max:60',
            'pairs_per_column' => 'required|integer|min:1|max:60',
            'time_per_column' => 'required|integer|min:10|max:300',
        ]);

        $pauliTest->update($validated);

        return redirect()
            ->route('admin.alat-tes.questions.index', $validated['alat_tes_id'])
            ->with('success', 'Konfigurasi Pauli Test berhasil diupdate!');
    }

    /**
     * Hapus Pauli Test
     */
    public function destroy(PauliTest $pauliTest)
    {
        $alatTesId = $pauliTest->alat_tes_id;
        $pauliTest->delete();
        
        return redirect()
            ->route('admin.alat-tes.questions.index', $alatTesId)
            ->with('success', 'Konfigurasi Pauli Test berhasil dihapus!');
    }

    /**
     * Tampilkan hasil test per konfigurasi
     * Route: /admin/pauli/{pauliTest}/results
     */
    public function results(PauliTest $pauliTest)
    {
        $results = $pauliTest->results()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('questions.index_pauli_result', compact('pauliTest', 'results'));
    }

    /**
     * Tampilkan detail hasil individual
     * Route: /admin/pauli/result/{resultId}
     */
    public function showResult($resultId)
    {
        $result = \App\Models\PauliResult::with(['user', 'pauliTest'])
            ->findOrFail($resultId);
            
        return view('questions.show_pauli_result', compact('result'));
    }
}
