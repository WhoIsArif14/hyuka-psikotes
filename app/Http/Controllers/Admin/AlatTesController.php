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
     * PERBAIKAN: Cascade delete - hapus semua soal terkait sebelum menghapus alat tes
     */
    public function destroy(AlatTes $alat_te)
    {
        try {
            DB::beginTransaction();

            $name = $alat_te->name;
            $deletedQuestionsCount = 0;

            // Hapus semua soal terkait terlebih dahulu
            if ($this->isPapiKostick($alat_te)) {
                // Hapus soal PAPI
                $deletedQuestionsCount = PapiQuestion::where('alat_tes_id', $alat_te->id)->count();
                PapiQuestion::where('alat_tes_id', $alat_te->id)->delete();

                Log::info('Deleted PAPI questions for Alat Tes', [
                    'alat_tes_id' => $alat_te->id,
                    'alat_tes_name' => $name,
                    'deleted_questions' => $deletedQuestionsCount
                ]);
            } else {
                // Hapus soal umum
                $deletedQuestionsCount = $alat_te->questions()->count();
                $alat_te->questions()->delete();

                Log::info('Deleted regular questions for Alat Tes', [
                    'alat_tes_id' => $alat_te->id,
                    'alat_tes_name' => $name,
                    'deleted_questions' => $deletedQuestionsCount
                ]);
            }

            // Hapus alat tes
            $alat_te->delete();

            DB::commit();

            Log::info('Alat Tes deleted successfully', [
                'name' => $name,
                'total_questions_deleted' => $deletedQuestionsCount
            ]);

            $message = $deletedQuestionsCount > 0
                ? "Alat Tes '{$name}' dan {$deletedQuestionsCount} soal berhasil dihapus."
                : "Alat Tes '{$name}' berhasil dihapus.";

            return redirect()->route('admin.alat-tes.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete Alat Tes', [
                'alat_tes_id' => $alat_te->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('error', 'Gagal menghapus Alat Tes: ' . $e->getMessage());
        }
    }
}
