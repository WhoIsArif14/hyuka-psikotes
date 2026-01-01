<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\PapiQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlatTesController extends Controller
{
    /**
     * Helper method untuk mengecek apakah Alat Tes adalah PAPI Kostick
     */
    private function isPapiKostick($alatTes)
    {
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
    // ✅ PERBAIKAN: Hitung SEMUA tipe soal termasuk PAPIKOSTICK & RMIB
    $AlatTes = AlatTes::withCount('questions')->latest()->get();
    
    // Jika Anda ingin tetap menggunakan manual mapping untuk custom logic:
    // $AlatTes = AlatTes::latest()->get()->map(function ($alatTes) {
    //     // Hitung SEMUA soal dari tabel questions (termasuk PAPIKOSTICK, RMIB, dll)
    //     $alatTes->questions_count = $alatTes->questions()->count();
    //     return $alatTes;
    // });

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
        // ✅ Kirim array kosong untuk contoh soal
        $examples = [];
        return view('admin.alat-tes.create', compact('examples'));
    }

    /**
     * Menyimpan Alat Tes baru ke database. (CREATE)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Hanya nama & durasi)
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        try {
            // 2. Persiapan Data (tidak menyimpan instruksi atau contoh soal di sini)
            $dataToCreate = [
                'name' => $validated['name'],
                'duration_minutes' => $validated['duration_minutes'],
                'slug' => Str::slug($validated['name']),
                'description' => null,
            ];

            // 3. Simpan Data ke Database
            $alatTes = AlatTes::create($dataToCreate);

            Log::info('Alat Tes created successfully', [
                'id' => $alatTes->id,
                'name' => $alatTes->name,
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes baru berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Failed to create Alat Tes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan Alat Tes. Pesan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menampilkan form untuk mengedit Alat Tes.
     */
    public function edit(AlatTes $alat_te)
    {
        // Return edit view (name + duration only)
        return view('admin.alat-tes.edit', [
            'AlatTes' => $alat_te,
        ]);
    }

    /**
     * Mengupdate Alat Tes di database. (UPDATE)
     */
    public function update(Request $request, AlatTes $alat_te)
    {
        // Validasi Input (Hanya nama & durasi)
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name,' . $alat_te->id,
            'duration_minutes' => 'required|integer|min:1',
        ]);

        try {
            // Persiapan Data
            $dataToUpdate = [
                'name' => $validated['name'],
                'duration_minutes' => $validated['duration_minutes'],
                'slug' => Str::slug($validated['name']),
            ];



            // Update Data
            $alat_te->update($dataToUpdate);

            Log::info('Alat Tes updated', [
                'id' => $alat_te->id,
                'name' => $alat_te->name,
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Failed to update Alat Tes', [
                'error' => $e->getMessage(),
                'alat_tes_id' => $alat_te->id
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal memperbarui: ' . $e->getMessage()
            ]);
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

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            try {
                if ($this->isPapiKostick($alat_te)) {
                    $deletedQuestionsCount = PapiQuestion::where('alat_tes_id', $alat_te->id)->count();
                    PapiQuestion::where('alat_tes_id', $alat_te->id)->delete();
                }

                $questionsCount = DB::table('questions')
                    ->where('alat_tes_id', $alat_te->id)
                    ->count();

                DB::table('questions')
                    ->where('alat_tes_id', $alat_te->id)
                    ->delete();

                $deletedQuestionsCount += $questionsCount;

                DB::table('question_options')->whereIn('question_id', function ($query) use ($alat_te) {
                    $query->select('id')
                        ->from('questions')
                        ->where('alat_tes_id', $alat_te->id);
                })->delete();

                $alat_te->delete();

                Log::info('Alat Tes deleted successfully', [
                    'id' => $alat_te->id,
                    'name' => $name,
                    'questions_deleted' => $deletedQuestionsCount
                ]);
            } finally {
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
