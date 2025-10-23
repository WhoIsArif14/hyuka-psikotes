<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\PapiQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlatTesController extends Controller
{
    /**
     * Menampilkan daftar semua Alat Tes.
     * PERBAIKAN: Menambahkan logic untuk menghitung soal PAPI
     */
    public function index()
    {
        // Ambil semua data Alat Tes dengan jumlah soal
        $AlatTes = AlatTes::latest()->get()->map(function ($alatTes) {
            // Cek apakah ini PAPI Kostick
            if ($this->isPapiKostick($alatTes)) {
                // Hitung soal dari tabel papi_questions
                $alatTes->questions_count = PapiQuestion::count();
            } else {
                // Hitung soal dari tabel questions biasa
                $alatTes->loadCount('questions');
            }
            
            return $alatTes;
        });

        // Convert ke paginator
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $pagedData = $AlatTes->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $AlatTes = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $AlatTes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.alat-tes.index', compact('AlatTes'));
    }

    /**
     * Helper method untuk mengecek apakah Alat Tes adalah PAPI Kostick
     */
    private function isPapiKostick($alatTes)
    {
        if (!isset($alatTes->slug)) {
            return false;
        }
        
        $slug = strtolower(trim($alatTes->slug));
        
        return in_array($slug, [
            'papi-kostick',
            'papikostick',
            'papi_kostick',
            'papi kostick'
        ]);
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
     * TETAP SIMPLE seperti kode lama, tapi dengan logging
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        try {
            $alatTes = AlatTes::create($validated);
            
            Log::info('Alat Tes created', ['id' => $alatTes->id, 'name' => $alatTes->name]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes baru berhasil dibuat.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create Alat Tes', ['error' => $e->getMessage()]);
            
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan form untuk mengedit Alat Tes.
     */
    public function edit(AlatTes $alat_te)
    {
        return view('admin.alat-tes.edit', ['AlatTes' => $alat_te]);
    }

    /**
     * Mengupdate Alat Tes di database.
     */
    public function update(Request $request, AlatTes $alat_te)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        try {
            $alat_te->update($validated);
            
            Log::info('Alat Tes updated', ['id' => $alat_te->id, 'name' => $alat_te->name]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes berhasil diperbarui.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update Alat Tes', ['error' => $e->getMessage()]);
            
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus Alat Tes dari database.
     * PERBAIKAN: Menambahkan proteksi untuk PAPI
     */
    public function destroy(AlatTes $alat_te)
    {
        try {
            // PROTEKSI: Jangan izinkan hapus PAPI Kostick jika ada soal
            if ($this->isPapiKostick($alat_te)) {
                $papiCount = PapiQuestion::count();
                
                if ($papiCount > 0) {
                    return back()->withErrors([
                        'error' => 'Tidak dapat menghapus Alat Tes PAPI Kostick karena masih ada ' . $papiCount . ' soal.'
                    ]);
                }
            } else {
                // Untuk alat tes biasa, cek jumlah soal
                $questionCount = $alat_te->questions()->count();
                
                if ($questionCount > 0) {
                    return back()->withErrors([
                        'error' => 'Tidak dapat menghapus Alat Tes karena masih ada ' . $questionCount . ' soal.'
                    ]);
                }
            }
            
            $name = $alat_te->name;
            $alat_te->delete();
            
            Log::info('Alat Tes deleted', ['name' => $name]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes berhasil dihapus.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete Alat Tes', ['error' => $e->getMessage()]);
            
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}