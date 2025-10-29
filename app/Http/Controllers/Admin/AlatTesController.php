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
     * PERBAIKAN: Menggunakan ID alat tes untuk menghitung soal PAPI.
     */
    public function index()
    {
        $AlatTes = AlatTes::latest()->get()->map(function ($alatTes) {

            if ($this->isPapiKostick($alatTes)) {
                // ✅ Hitung soal PAPI berdasarkan alat_tes_id
                $alatTes->questions_count = PapiQuestion::where('alat_tes_id', $alatTes->id)->count();

                // ✅ Debug log
                \Log::info('PAPI Count', [
                    'alat_tes_id' => $alatTes->id,
                    'name' => $alatTes->name,
                    'count' => $alatTes->questions_count
                ]);
            } else {
                // ✅ Hitung soal umum
                $alatTes->questions_count = $alatTes->questions()->count();

                // ✅ Debug log
                \Log::info('Regular Questions Count', [
                    'alat_tes_id' => $alatTes->id,
                    'name' => $alatTes->name,
                    'count' => $alatTes->questions_count
                ]);
            }

            return $alatTes;
        });

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
        // Cek slug
        if (isset($alatTes->slug) && !empty($alatTes->slug)) {
            $slug = strtolower(trim($alatTes->slug));
            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                return true;
            }
        }

        // Cek name jika slug tidak ada
        if (isset($alatTes->name) && !empty($alatTes->name)) {
            $name = strtolower(trim($alatTes->name));
            if (str_contains($name, 'papi') || str_contains($name, 'kostick') || str_contains($name, 'mami')) {
                return true;
            }
        }

        return false;
    }

    public function create() // <-- METHOD INI YANG HILANG ATAU SALAH NAMA
    {
        // Biasanya, di sini Anda hanya mengembalikan view yang berisi form input.
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
     */
    public function destroy(AlatTes $alat_te)
    {
        try {
            if ($this->isPapiKostick($alat_te)) {
                // Perbaikan: Hitung soal PAPI yang terkait dengan ID Alat Tes ini.
                $papiCount = PapiQuestion::where('alat_tes_id', $alat_te->id)->count();

                if ($papiCount > 0) {
                    return back()->withErrors([
                        'error' => 'Tidak dapat menghapus Alat Tes PAPI Kostick karena masih ada ' . $papiCount . ' soal.'
                    ]);
                }
            } else {
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
