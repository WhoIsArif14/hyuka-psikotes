<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RmibResult;
use App\Models\AlatTes;
use Illuminate\Http\Request;

class RmibResultController extends Controller
{
    /**
     * Tampilkan daftar semua hasil RMIB
     */
    public function index(Request $request)
    {
        $query = RmibResult::with(['user', 'alatTes'])
            ->orderBy('completed_at', 'desc');

        // Filter by Alat Tes
        if ($request->filled('alat_tes_id')) {
            $query->where('alat_tes_id', $request->alat_tes_id);
        }

        // Search by user name
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $results = $query->paginate(20);
        $alatTesList = AlatTes::whereHas('rmibResults')->get();

        return view('questions.index_rmib_results', compact('results', 'alatTesList'));
    }

    /**
     * Tampilkan detail hasil RMIB
     */
    public function show(RmibResult $result)
    {
        $result->load('user', 'alatTes');
        
        return view('questions.show_rmib_result', compact('result'));
    }

    /**
     * Export hasil RMIB ke PDF/Excel
     */
    public function export(RmibResult $result, $format = 'pdf')
    {
        // TODO: Implementasi export PDF/Excel
        // Bisa menggunakan library seperti Laravel Excel atau DomPDF
        
        return back()->with('info', 'Fitur export dalam pengembangan');
    }

    /**
     * Hapus hasil RMIB
     */
    public function destroy(RmibResult $result)
    {
        try {
            $userName = $result->user->name;
            $result->delete();

            return redirect()
                ->route('admin.rmib-results.index')
                ->with('success', "Hasil RMIB dari {$userName} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus hasil: ' . $e->getMessage());
        }
    }
}