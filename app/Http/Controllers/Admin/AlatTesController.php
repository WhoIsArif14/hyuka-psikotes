<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use Illuminate\Http\Request;

class AlatTesController extends Controller
{
    /**
     * Menampilkan daftar semua Alat Tes.
     */
    public function index()
    {
        // Ambil semua data Alat Tes, hitung jumlah soal di dalamnya, dan urutkan dari yang terbaru
        $AlatTes = AlatTes::withCount('questions')->latest()->paginate(10);
        return view('admin.alat-tes.index', compact('AlatTes'));
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

        AlatTes::create($validated);

        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit Alat Tes.
     */
    public function edit(AlatTes $AlatTes) // Laravel 8+ Route Model Binding
    {
        return view('admin.alat-tes.edit', ['AlatTes' => $AlatTes]);
    }

    /**
     * Mengupdate Alat Tes di database.
     */
    public function update(Request $request, AlatTes $AlatTes)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $AlatTes->update($validated);

        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes berhasil diperbarui.');
    }

    /**
     * Menghapus Alat Tes dari database.
     */
    public function destroy(AlatTes $AlatTes)
    {
        $AlatTes->delete();
        return redirect()->route('admin.alat-tes.index')->with('success', 'Alat Tes berhasil dihapus.');
    }
}
