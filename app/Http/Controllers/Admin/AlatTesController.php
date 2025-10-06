<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\alatTes;
use Illuminate\Http\Request;

class alatTesController extends Controller
{
    /**
     * Menampilkan daftar semua Alat Tes.
     */
    public function index()
    {
        // Ambil semua data Alat Tes, hitung jumlah soal di dalamnya, dan urutkan dari yang terbaru
        $alatTes = alatTes::withCount('questions')->latest()->paginate(10);
        return view('admin.alat-tes.index', compact('alatTes'));
    }

    /**
     * Menampilkan form untuk membuat Alat Tes baru.
     */
    public function create()
    {
        return view('admin.alat-tes.create');
    }

    /**
     * Menyimpan Alat Tes baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        alatTes::create($validated);

        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit Alat Tes.
     */
    public function edit(alatTes $alatTes) // Laravel 8+ Route Model Binding
    {
        return view('admin.alat-tes.edit', ['alatTes' => $alatTes]);
    }

    /**
     * Mengupdate Alat Tes di database.
     */
    public function update(Request $request, alatTes $alatTes)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $alatTes->update($validated);

        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes berhasil diperbarui.');
    }

    /**
     * Menghapus Alat Tes dari database.
     */
    public function destroy(alatTes $alatTes)
    {
        $alatTes->delete();
        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes berhasil dihapus.');
    }
}
