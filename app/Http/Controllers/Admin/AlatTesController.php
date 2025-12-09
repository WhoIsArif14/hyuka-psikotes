<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\PapiQuestion; // Diasumsikan untuk tipe tes PAPI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // PENTING: Untuk helper Str::slug()

class AlatTesController extends Controller
{
    /**
     * Helper method untuk mengecek apakah Alat Tes adalah PAPI Kostick
     * Metode ini digunakan di index dan destroy.
     * @param AlatTes $alatTes
     * @return bool
     */
    private function isPapiKostick($alatTes)
    {
        // Cek berdasarkan slug atau nama (sesuai logika yang Anda berikan di Model)
        if (isset($alatTes->slug) && !empty($alatTes->slug)) {
            $slug = strtolower(trim($alatTes->slug));
            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                return true;
            }
        }

        if (isset($alatTes->name) && !empty($alatTes->name)) {
            $name = strtolower(trim($alatTes->name));
            if (str_contains($name, 'papi') || str_contains($name, 'kostick') || str_contains($name, 'mami')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Menampilkan daftar semua Alat Tes (READ).
     */
    public function index(Request $request)
    {
        // Mengambil semua Alat Tes dan menghitung soal terkait
        $AlatTes = AlatTes::latest()->get()->map(function ($alatTes) {

            if ($this->isPapiKostick($alatTes)) {
                // Hitung soal PAPI
                $alatTes->questions_count = PapiQuestion::where('alat_tes_id', $alatTes->id)->count();
            } else {
                // Hitung soal umum
                $alatTes->questions_count = $alatTes->questions()->count();
            }

            return $alatTes;
        });

        // Implementasi Pagination manual dari Collection
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $pagedData = $AlatTes->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $AlatTes = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $AlatTes->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
     * Menyimpan Alat Tes baru ke database. (CREATE)
     * FIX: Membuat slug otomatis dan menangani kolom 'description'.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input. Tambahkan 'unique' untuk name agar tidak ada duplikasi.
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name',
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        try {
            // 2. Persiapan Data
            $dataToCreate = $validated;

            // Membuat slug otomatis dari name
            $dataToCreate['slug'] = Str::slug($validated['name']);

            // Mengisi 'description' (jika tidak dikirim dari form, set null/default)
            // Asumsi 'description' di database BISA NULL.
            if (in_array('description', (new AlatTes())->getFillable()) && !isset($dataToCreate['description'])) {
                 $dataToCreate['description'] = null;
            }

            // 3. Simpan Data ke Database
            $alatTes = AlatTes::create($dataToCreate);

            Log::info('Alat Tes created successfully', ['id' => $alatTes->id, 'name' => $alatTes->name]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes baru berhasil dibuat.');
        } catch (\Exception $e) {
            // Log Error
            Log::error('Failed to create Alat Tes', ['error' => $e->getMessage()]);

            // Kembali ke form dengan input lama dan error spesifik
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan Alat Tes. Pesan database: ' . $e->getMessage()]);
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
     * Mengupdate Alat Tes di database. (UPDATE)
     */
    public function update(Request $request, AlatTes $alat_te)
    {
        // Validasi Input. Abaikan ID saat ini untuk aturan 'unique'.
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name,' . $alat_te->id,
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        try {
            // Persiapan Data (Update slug otomatis)
            $dataToUpdate = $validated;
            $dataToUpdate['slug'] = Str::slug($validated['name']);

            // Update Data
            $alat_te->update($dataToUpdate);

            Log::info('Alat Tes updated', ['id' => $alat_te->id, 'name' => $alat_te->name]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Failed to update Alat Tes', ['error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus Alat Tes dari database. (DELETE)
     */
    public function destroy(AlatTes $alat_te)
{
    try {
        DB::beginTransaction();

        $name = $alat_te->name;
        $deletedQuestionsCount = 0;

        // ✅ DISABLE FOREIGN KEY CHECK SEMENTARA
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Hapus soal PAPI jika ada
            if ($this->isPapiKostick($alat_te)) {
                $deletedQuestionsCount = PapiQuestion::where('alat_tes_id', $alat_te->id)->count();
                PapiQuestion::where('alat_tes_id', $alat_te->id)->delete();
            }
            
            // Hapus SEMUA soal dari tabel questions (termasuk RMIB, dll)
            $questionsCount = DB::table('questions')
                ->where('alat_tes_id', $alat_te->id)
                ->count();
            
            DB::table('questions')
                ->where('alat_tes_id', $alat_te->id)
                ->delete();
            
            $deletedQuestionsCount += $questionsCount;

            // Hapus dari tabel lain yang mungkin terkait
            // (sesuaikan dengan struktur database Anda)
            DB::table('question_options')->whereIn('question_id', function($query) use ($alat_te) {
                $query->select('id')
                      ->from('questions')
                      ->where('alat_tes_id', $alat_te->id);
            })->delete();

            // Hapus alat tes
            $alat_te->delete();

            Log::info('Alat Tes deleted successfully', [
                'id' => $alat_te->id,
                'name' => $name,
                'questions_deleted' => $deletedQuestionsCount
            ]);

        } finally {
            // ✅ ENABLE KEMBALI FOREIGN KEY CHECK
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        DB::commit();

        $message = $deletedQuestionsCount > 0
            ? "Alat Tes '{$name}' dan {$deletedQuestionsCount} soal berhasil dihapus."
            : "Alat Tes '{$name}' berhasil dihapus.";

        return redirect()->route('admin.alat-tes.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // ✅ PASTIKAN FOREIGN KEY CHECK KEMBALI AKTIF
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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